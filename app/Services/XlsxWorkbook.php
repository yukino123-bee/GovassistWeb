<?php

namespace App\Services;

use RuntimeException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use ZipArchive;

/**
 * Minimal streaming .xlsx writer. Rows are written to temporary files as they are added,
 * so large exports do not have to be held in memory. All text is stored as plain strings,
 * which means Excel never evaluates a value as a formula.
 */
class XlsxWorkbook
{
    /** @var array<int, array{name: string, path: string, handle: resource, rows: int, columns: int, widths: array<int, int>, header: bool, links: array<int, array{ref: string, url: string}>}> */
    private array $sheets = [];

    private ?int $currentSheet = null;

    /**
     * Start a new worksheet. With $headers, the first row is styled, frozen and filterable.
     *
     * @param  array<int, string>|null  $headers
     * @param  array<int, int>  $columnWidths  Widths in characters; unspecified columns are sized from the header.
     */
    public function addSheet(string $name, ?array $headers = null, array $columnWidths = []): static
    {
        $path = tempnam(sys_get_temp_dir(), 'govassist-sheet-');
        $handle = fopen($path, 'wb');

        if ($path === false || $handle === false) {
            throw new RuntimeException('Unable to create a temporary spreadsheet file.');
        }

        $this->sheets[] = [
            'name' => $this->safeSheetName($name, count($this->sheets) + 1),
            'path' => $path,
            'handle' => $handle,
            'rows' => 0,
            'columns' => 0,
            'widths' => $columnWidths,
            'header' => $headers !== null,
            'links' => [],
        ];
        $this->currentSheet = array_key_last($this->sheets);

        if ($headers !== null) {
            foreach ($headers as $index => $header) {
                $this->sheets[$this->currentSheet]['widths'][$index] ??= max(12, mb_strlen($header) + 4);
            }
            $this->writeRow($headers, 'header');
        }

        return $this;
    }

    /**
     * Cells may be a scalar, or ['text' => ..., 'url' => ...] for a clickable link.
     *
     * @param  array<int, string|int|float|array{text: string, url: string}|null>  $cells
     * @param  'header'|'title'|'label'|'section'|null  $style
     */
    public function addRow(array $cells, ?string $style = null): static
    {
        $this->writeRow($cells, $style);

        return $this;
    }

    public function addBlankRow(): static
    {
        $this->writeRow([], null);

        return $this;
    }

    public function download(string $filename): BinaryFileResponse
    {
        $path = $this->save();

        return response()->download($path, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Assemble the workbook and return the path of the finished file.
     */
    public function save(): string
    {
        if ($this->sheets === []) {
            $this->addSheet('Sheet1');
        }

        $zipPath = tempnam(sys_get_temp_dir(), 'govassist-xlsx-');
        $zip = new ZipArchive;

        if ($zip->open($zipPath, ZipArchive::OVERWRITE) !== true) {
            throw new RuntimeException('Unable to create the spreadsheet archive.');
        }

        $zip->addFromString('[Content_Types].xml', $this->contentTypes());
        $zip->addFromString('_rels/.rels', $this->rootRelationships());
        $zip->addFromString('xl/workbook.xml', $this->workbookXml());
        $zip->addFromString('xl/_rels/workbook.xml.rels', $this->workbookRelationships());
        $zip->addFromString('xl/styles.xml', $this->stylesXml());

        foreach ($this->sheets as $index => $sheet) {
            fclose($sheet['handle']);
            $zip->addFromString('xl/worksheets/sheet'.($index + 1).'.xml', $this->sheetXml($sheet));

            if ($sheet['links'] !== []) {
                $zip->addFromString('xl/worksheets/_rels/sheet'.($index + 1).'.xml.rels', $this->sheetRelationships($sheet['links']));
            }
        }

        $zip->close();

        foreach ($this->sheets as $sheet) {
            @unlink($sheet['path']);
        }

        $this->sheets = [];

        return $zipPath;
    }

    /**
     * @param  array<int, string|int|float|array{text: string, url: string}|null>  $cells
     */
    private function writeRow(array $cells, ?string $style): void
    {
        $sheet = &$this->sheets[$this->currentSheet];
        $sheet['rows']++;
        $sheet['columns'] = max($sheet['columns'], count($cells));

        $styleIndex = match ($style) {
            'header' => 1,
            'title' => 2,
            'label' => 3,
            'section' => 4,
            default => 0,
        };

        $xml = '<row r="'.$sheet['rows'].'">';

        foreach (array_values($cells) as $column => $value) {
            $reference = $this->columnLetter($column).$sheet['rows'];
            $styleAttribute = $styleIndex > 0 ? ' s="'.$styleIndex.'"' : '';

            if (is_array($value)) {
                $sheet['links'][] = ['ref' => $reference, 'url' => (string) $value['url']];
                $xml .= '<c r="'.$reference.'" s="5" t="inlineStr"><is><t xml:space="preserve">'.$this->escape((string) $value['text']).'</t></is></c>';
            } elseif ($value === null || $value === '') {
                $xml .= '<c r="'.$reference.'"'.$styleAttribute.'/>';
            } elseif (is_int($value) || is_float($value)) {
                $xml .= '<c r="'.$reference.'"'.$styleAttribute.'><v>'.$value.'</v></c>';
            } else {
                $xml .= '<c r="'.$reference.'"'.$styleAttribute.' t="inlineStr"><is><t xml:space="preserve">'.$this->escape((string) $value).'</t></is></c>';
            }
        }

        fwrite($sheet['handle'], $xml.'</row>');
    }

    /**
     * @param  array{name: string, path: string, rows: int, columns: int, widths: array<int, int>, header: bool, links: array<int, array{ref: string, url: string}>}  $sheet
     */
    private function sheetXml(array $sheet): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">';

        $xml .= '<sheetViews><sheetView workbookViewId="0"'.($sheet['header'] ? '' : ' showGridLines="0"').'>';
        if ($sheet['header']) {
            $xml .= '<pane ySplit="1" topLeftCell="A2" activePane="bottomLeft" state="frozen"/>';
        }
        $xml .= '</sheetView></sheetViews><sheetFormatPr defaultRowHeight="15"/>';

        if ($sheet['widths'] !== []) {
            $xml .= '<cols>';
            ksort($sheet['widths']);
            foreach ($sheet['widths'] as $index => $width) {
                $xml .= '<col min="'.($index + 1).'" max="'.($index + 1).'" width="'.$width.'" customWidth="1"/>';
            }
            $xml .= '</cols>';
        }

        $xml .= '<sheetData>'.file_get_contents($sheet['path']).'</sheetData>';

        if ($sheet['header'] && $sheet['rows'] > 1 && $sheet['columns'] > 0) {
            $xml .= '<autoFilter ref="A1:'.$this->columnLetter($sheet['columns'] - 1).$sheet['rows'].'"/>';
        }

        if ($sheet['links'] !== []) {
            $xml .= '<hyperlinks>';
            foreach ($sheet['links'] as $index => $link) {
                $xml .= '<hyperlink ref="'.$link['ref'].'" r:id="rId'.($index + 1).'"/>';
            }
            $xml .= '</hyperlinks>';
        }

        return $xml.'</worksheet>';
    }

    /**
     * @param  array<int, array{ref: string, url: string}>  $links
     */
    private function sheetRelationships(array $links): string
    {
        $relationships = '';
        foreach ($links as $index => $link) {
            $relationships .= '<Relationship Id="rId'.($index + 1).'" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/hyperlink" Target="'.$this->escape($link['url']).'" TargetMode="External"/>';
        }

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'.$relationships.'</Relationships>';
    }

    private function stylesXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            .'<fonts count="5">'
            .'<font><sz val="11"/><name val="Calibri"/></font>'
            .'<font><b/><sz val="11"/><color rgb="FFFFFFFF"/><name val="Calibri"/></font>'
            .'<font><b/><sz val="16"/><color rgb="FF7F1D1D"/><name val="Calibri"/></font>'
            .'<font><b/><sz val="11"/><name val="Calibri"/></font>'
            .'<font><u/><sz val="11"/><color rgb="FF1D4ED8"/><name val="Calibri"/></font>'
            .'</fonts>'
            .'<fills count="4">'
            .'<fill><patternFill patternType="none"/></fill>'
            .'<fill><patternFill patternType="gray125"/></fill>'
            .'<fill><patternFill patternType="solid"><fgColor rgb="FFB91C1C"/><bgColor indexed="64"/></patternFill></fill>'
            .'<fill><patternFill patternType="solid"><fgColor rgb="FFF1F5F9"/><bgColor indexed="64"/></patternFill></fill>'
            .'</fills>'
            .'<borders count="1"><border><left/><right/><top/><bottom/><diagonal/></border></borders>'
            .'<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            .'<cellXfs count="6">'
            .'<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>'
            .'<xf numFmtId="0" fontId="1" fillId="2" borderId="0" xfId="0" applyFont="1" applyFill="1" applyAlignment="1"><alignment vertical="center" wrapText="1"/></xf>'
            .'<xf numFmtId="0" fontId="2" fillId="0" borderId="0" xfId="0" applyFont="1"/>'
            .'<xf numFmtId="0" fontId="3" fillId="0" borderId="0" xfId="0" applyFont="1"/>'
            .'<xf numFmtId="0" fontId="3" fillId="3" borderId="0" xfId="0" applyFont="1" applyFill="1"/>'
            .'<xf numFmtId="0" fontId="4" fillId="0" borderId="0" xfId="0" applyFont="1"/>'
            .'</cellXfs>'
            .'<cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles>'
            .'</styleSheet>';
    }

    private function contentTypes(): string
    {
        $sheets = '';
        foreach (array_keys($this->sheets) as $index) {
            $sheets .= '<Override PartName="/xl/worksheets/sheet'.($index + 1).'.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>';
        }

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            .'<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            .'<Default Extension="xml" ContentType="application/xml"/>'
            .'<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            .'<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            .$sheets
            .'</Types>';
    }

    private function rootRelationships(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            .'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            .'</Relationships>';
    }

    private function workbookXml(): string
    {
        $sheets = '';
        foreach ($this->sheets as $index => $sheet) {
            $sheets .= '<sheet name="'.$this->escape($sheet['name']).'" sheetId="'.($index + 1).'" r:id="rId'.($index + 1).'"/>';
        }

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            .'<sheets>'.$sheets.'</sheets></workbook>';
    }

    private function workbookRelationships(): string
    {
        $relationships = '';
        foreach (array_keys($this->sheets) as $index) {
            $relationships .= '<Relationship Id="rId'.($index + 1).'" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet'.($index + 1).'.xml"/>';
        }
        $relationships .= '<Relationship Id="rId'.(count($this->sheets) + 1).'" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>';

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'.$relationships.'</Relationships>';
    }

    private function columnLetter(int $index): string
    {
        $letter = '';
        for ($index++; $index > 0; $index = intdiv($index - 1, 26)) {
            $letter = chr(65 + ($index - 1) % 26).$letter;
        }

        return $letter;
    }

    private function escape(string $value): string
    {
        $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/u', '', $value) ?? '';

        return htmlspecialchars(mb_substr($value, 0, 32000), ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }

    private function safeSheetName(string $name, int $position): string
    {
        $name = trim(str_replace(['\\', '/', '?', '*', '[', ']', ':'], ' ', $name));

        return mb_substr($name !== '' ? $name : 'Sheet'.$position, 0, 31);
    }
}
