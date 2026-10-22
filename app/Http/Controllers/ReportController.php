<?php

namespace App\Http\Controllers;

use App\Models\EligibilityAssessment;
use App\Models\GovernmentService;
use App\Models\User;
use App\Models\UserChecklist;
use App\Models\UserInquiry;
use App\Services\XlsxWorkbook;
use Carbon\CarbonInterface;
use Illuminate\Contracts\Database\Eloquent\Builder as BuilderContract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportController extends Controller
{
    public function index()
    {
        return view('facilitator.reports.index', [
            'totalApplications' => UserChecklist::count(),
            'approvedApplications' => UserChecklist::where('status', 'approved')->count(),
            'pendingApplications' => UserChecklist::where('status', 'pending')->count(),
            'rejectedApplications' => UserChecklist::where('status', 'rejected')->count(),
            'totalResidents' => User::where('role', 'resident')->count(),
            'verifiedResidents' => User::where('role', 'resident')->whereNotNull('valid_id_path')->count(),
            'totalAssessments' => EligibilityAssessment::count(),
            'eligibleAssessments' => EligibilityAssessment::where('status', 'eligible')->count(),
            'totalInquiries' => UserInquiry::count(),
            'pendingInquiries' => UserInquiry::where('status', 'pending')->count(),
            'services' => GovernmentService::all(),
        ]);
    }

    public function exportApplications(Request $request): BinaryFileResponse
    {
        $filters = $request->validate([
            ...$this->dateRules(),
            'status' => ['nullable', Rule::in(['pending', 'approved', 'rejected', 'draft'])],
            'service_id' => ['nullable', 'integer', 'exists:government_services,id'],
        ]);

        $workbook = (new XlsxWorkbook)->addSheet('Applications', $this->applicationHeaders(), [0 => 14, 1 => 28, 2 => 30, 4 => 32, 9 => 40, 11 => 20]);
        $this->writeApplications($workbook, $this->applicationsQuery($filters));

        return $workbook->download($this->filename('applications', $filters));
    }

    public function exportResidents(Request $request): BinaryFileResponse
    {
        $filters = $request->validate([
            ...$this->dateRules(),
            'verified' => ['nullable', Rule::in(['yes', 'no'])],
        ]);

        $workbook = (new XlsxWorkbook)->addSheet('Residents', $this->residentHeaders(), [0 => 10, 1 => 28, 2 => 30, 6 => 40]);
        $this->writeResidents($workbook, $this->residentsQuery($filters));

        return $workbook->download($this->filename('residents', $filters));
    }

    public function exportAssessments(Request $request): BinaryFileResponse
    {
        $filters = $request->validate([
            ...$this->dateRules(),
            'status' => ['nullable', Rule::in(['eligible', 'ineligible'])],
        ]);

        $workbook = (new XlsxWorkbook)->addSheet('Eligibility', $this->assessmentHeaders(), [1 => 28, 2 => 30, 3 => 32]);
        $this->writeAssessments($workbook, $this->assessmentsQuery($filters));

        return $workbook->download($this->filename('eligibility', $filters));
    }

    public function exportInquiries(Request $request): BinaryFileResponse
    {
        $filters = $request->validate([
            ...$this->dateRules(),
            'status' => ['nullable', Rule::in(['pending', 'in_progress', 'resolved', 'closed'])],
        ]);

        $workbook = (new XlsxWorkbook)->addSheet('Inquiries', $this->inquiryHeaders(), [1 => 28, 2 => 30, 3 => 32, 4 => 36, 5 => 60]);
        $this->writeInquiries($workbook, $this->inquiriesQuery($filters));

        return $workbook->download($this->filename('inquiries', $filters));
    }

    /**
     * One workbook with a summary sheet plus every dataset on its own sheet.
     */
    public function exportAllMasterReport(Request $request): BinaryFileResponse
    {
        $filters = $request->validate($this->dateRules());

        $workbook = new XlsxWorkbook;
        $this->writeSummary($workbook, $filters);

        $workbook->addSheet('Applications', $this->applicationHeaders(), [0 => 14, 1 => 28, 2 => 30, 4 => 32, 9 => 40, 11 => 20]);
        $this->writeApplications($workbook, $this->applicationsQuery($filters));

        $workbook->addSheet('Residents', $this->residentHeaders(), [0 => 10, 1 => 28, 2 => 30, 6 => 40]);
        $this->writeResidents($workbook, $this->residentsQuery($filters));

        $workbook->addSheet('Eligibility', $this->assessmentHeaders(), [1 => 28, 2 => 30, 3 => 32]);
        $this->writeAssessments($workbook, $this->assessmentsQuery($filters));

        $workbook->addSheet('Inquiries', $this->inquiryHeaders(), [1 => 28, 2 => 30, 3 => 32, 4 => 36, 5 => 60]);
        $this->writeInquiries($workbook, $this->inquiriesQuery($filters));

        return $workbook->download($this->filename('master-report', $filters));
    }

    // --- Datasets -----------------------------------------------------------

    /**
     * @return array<int, string>
     */
    private function applicationHeaders(): array
    {
        return ['Application No.', 'Resident', 'Email', 'Contact Number', 'Program', 'Type', 'Status', 'Documents Approved', 'Submitted', 'Remarks', 'Last Updated', 'Review Documents'];
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    private function applicationsQuery(array $filters): BuilderContract
    {
        return $this->applyDateRange(
            UserChecklist::query()
                ->with(['user:id,name,email,contact_number', 'service'])
                ->withCount(['items as submitted_items_count' => fn ($query) => $query->whereNotNull('file_path')])
                ->withCount(['items as approved_items_count' => fn ($query) => $query->where('status', 'approved')])
                ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
                ->when($filters['service_id'] ?? null, fn ($query, $serviceId) => $query->where('service_id', $serviceId)),
            $filters,
        );
    }

    private function writeApplications(XlsxWorkbook $workbook, BuilderContract $query): void
    {
        $query->orderByDesc('created_at')->chunkById(500, function ($applications) use ($workbook): void {
            foreach ($applications as $application) {
                $workbook->addRow([
                    $application->id,
                    $application->user?->name ?? 'Deleted account',
                    $application->user?->email,
                    $application->user?->contact_number,
                    $application->service?->name_en ?? 'Deleted program',
                    Str::headline($application->application_type ?? 'new'),
                    Str::headline($application->status),
                    $application->approved_items_count.' of '.$application->submitted_items_count,
                    $this->date($application->created_at),
                    $application->remarks,
                    $this->date($application->updated_at),
                    ['text' => 'Open application', 'url' => route('facilitator.applications.show', $application)],
                ]);
            }
        });
    }

    /**
     * @return array<int, string>
     */
    private function residentHeaders(): array
    {
        return ['Resident No.', 'Name', 'Email', 'Contact Number', 'Civil Status', 'Date of Birth', 'Address', 'Valid ID (login required)', 'Email Verified', 'Applications', 'Registered'];
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    private function residentsQuery(array $filters): BuilderContract
    {
        return $this->applyDateRange(
            User::query()
                ->where('role', 'resident')
                ->withCount('checklists')
                ->when(($filters['verified'] ?? null) === 'yes', fn ($query) => $query->whereNotNull('valid_id_path'))
                ->when(($filters['verified'] ?? null) === 'no', fn ($query) => $query->whereNull('valid_id_path')),
            $filters,
        );
    }

    private function writeResidents(XlsxWorkbook $workbook, BuilderContract $query): void
    {
        $query->orderBy('name')->chunkById(500, function ($residents) use ($workbook): void {
            foreach ($residents as $resident) {
                $workbook->addRow([
                    $resident->id,
                    $resident->name,
                    $resident->email,
                    $resident->contact_number,
                    $resident->civil_status,
                    $resident->dob?->format('Y-m-d'),
                    $resident->address,
                    $resident->valid_id_path
                        ? ['text' => 'View ID', 'url' => route('facilitator.users.valid_id', $resident)]
                        : 'Not uploaded',
                    $resident->email_verified_at ? 'Yes' : 'No',
                    $resident->checklists_count,
                    $this->date($resident->created_at),
                ]);
            }
        });
    }

    /**
     * @return array<int, string>
     */
    private function assessmentHeaders(): array
    {
        return ['Assessment No.', 'Resident', 'Email', 'Program', 'Result', 'Taken'];
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    private function assessmentsQuery(array $filters): BuilderContract
    {
        return $this->applyDateRange(
            EligibilityAssessment::query()
                ->with(['user:id,name,email', 'service'])
                ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status)),
            $filters,
        );
    }

    private function writeAssessments(XlsxWorkbook $workbook, BuilderContract $query): void
    {
        $query->orderByDesc('created_at')->chunkById(500, function ($assessments) use ($workbook): void {
            foreach ($assessments as $assessment) {
                $workbook->addRow([
                    $assessment->id,
                    $assessment->user?->name ?? 'Deleted account',
                    $assessment->user?->email,
                    $assessment->service?->name_en ?? 'Deleted program',
                    Str::headline($assessment->status),
                    $this->date($assessment->created_at),
                ]);
            }
        });
    }

    /**
     * @return array<int, string>
     */
    private function inquiryHeaders(): array
    {
        return ['Ticket No.', 'Sender', 'Email', 'Program', 'Subject', 'Message', 'Status', 'Replies', 'Opened', 'Last Activity'];
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    private function inquiriesQuery(array $filters): BuilderContract
    {
        return $this->applyDateRange(
            UserInquiry::query()
                ->with(['user:id,name,email', 'service'])
                ->withCount('responses')
                ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status)),
            $filters,
        );
    }

    private function writeInquiries(XlsxWorkbook $workbook, BuilderContract $query): void
    {
        $query->orderByDesc('created_at')->chunkById(500, function ($inquiries) use ($workbook): void {
            foreach ($inquiries as $inquiry) {
                $workbook->addRow([
                    $inquiry->ticket_number ?? $inquiry->id,
                    $inquiry->user?->name ?? $inquiry->guest_name ?? 'Guest',
                    $inquiry->user?->email ?? $inquiry->guest_email,
                    $inquiry->service?->name_en ?? 'General inquiry',
                    $inquiry->subject,
                    $inquiry->inquiry_text,
                    Str::headline($inquiry->status),
                    $inquiry->responses_count,
                    $this->date($inquiry->created_at),
                    $this->date($inquiry->updated_at),
                ]);
            }
        });
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    private function writeSummary(XlsxWorkbook $workbook, array $filters): void
    {
        $period = match (true) {
            isset($filters['from'], $filters['to']) => $filters['from'].' to '.$filters['to'],
            isset($filters['from']) => 'From '.$filters['from'],
            isset($filters['to']) => 'Up to '.$filters['to'],
            default => 'All dates',
        };

        $applications = fn () => $this->applyDateRange(UserChecklist::query(), $filters);
        $assessments = fn () => $this->applyDateRange(EligibilityAssessment::query(), $filters);

        $workbook->addSheet('Summary', null, [0 => 38, 1 => 16, 2 => 16, 3 => 16, 4 => 16]);
        $workbook->addRow(['GovAssist Master Report'], 'title')
            ->addRow(['Period', $period])
            ->addRow(['Generated', now()->format('Y-m-d H:i')])
            ->addRow(['Generated by', auth()->user()?->name])
            ->addBlankRow()
            ->addRow(['Key figures', 'Count'], 'section')
            ->addRow(['Registered residents', $this->applyDateRange(User::query()->where('role', 'resident'), $filters)->count()])
            ->addRow(['Applications', $applications()->count()])
            ->addRow(['  Pending review', $applications()->where('status', 'pending')->count()])
            ->addRow(['  Approved', $applications()->where('status', 'approved')->count()])
            ->addRow(['  Rejected', $applications()->where('status', 'rejected')->count()])
            ->addRow(['Eligibility assessments', $assessments()->count()])
            ->addRow(['  Eligible', $assessments()->where('status', 'eligible')->count()])
            ->addRow(['  Not eligible', $assessments()->where('status', 'ineligible')->count()])
            ->addRow(['Inquiries', $this->applyDateRange(UserInquiry::query(), $filters)->count()])
            ->addBlankRow()
            ->addRow(['Applications by program', 'Total', 'Pending', 'Approved', 'Rejected'], 'section');

        $byProgram = $this->applyDateRange(UserChecklist::query(), $filters)
            ->select('service_id', 'status', DB::raw('count(*) as total'))
            ->groupBy('service_id', 'status')
            ->get()
            ->groupBy('service_id');

        $services = GovernmentService::all()->keyBy('id');

        foreach ($byProgram as $serviceId => $rows) {
            $count = fn (string $status): int => (int) ($rows->firstWhere('status', $status)?->total ?? 0);
            $workbook->addRow([
                $services->get($serviceId)?->name_en ?? 'Deleted program',
                (int) $rows->sum('total'),
                $count('pending'),
                $count('approved'),
                $count('rejected'),
            ]);
        }

        $workbook->addBlankRow()->addRow(['Eligibility by program', 'Total', 'Eligible', 'Not eligible'], 'section');

        $assessmentsByProgram = $this->applyDateRange(EligibilityAssessment::query(), $filters)
            ->select('service_id', 'status', DB::raw('count(*) as total'))
            ->groupBy('service_id', 'status')
            ->get()
            ->groupBy('service_id');

        foreach ($assessmentsByProgram as $serviceId => $rows) {
            $count = fn (string $status): int => (int) ($rows->firstWhere('status', $status)?->total ?? 0);
            $workbook->addRow([
                $services->get($serviceId)?->name_en ?? 'Deleted program',
                (int) $rows->sum('total'),
                $count('eligible'),
                $count('ineligible'),
            ]);
        }
    }

    // --- Helpers ------------------------------------------------------------

    /**
     * @return array<string, array<int, mixed>>
     */
    private function dateRules(): array
    {
        return [
            'from' => ['nullable', 'date_format:Y-m-d'],
            'to' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:from'],
        ];
    }

    /**
     * @template TBuilder of BuilderContract
     *
     * @param  TBuilder  $query
     * @param  array<string, mixed>  $filters
     * @return TBuilder
     */
    private function applyDateRange(BuilderContract $query, array $filters): BuilderContract
    {
        return $query
            ->when($filters['from'] ?? null, fn ($builder, $from) => $builder->whereDate('created_at', '>=', $from))
            ->when($filters['to'] ?? null, fn ($builder, $to) => $builder->whereDate('created_at', '<=', $to));
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    private function filename(string $name, array $filters): string
    {
        $suffix = collect([$filters['from'] ?? null, $filters['to'] ?? null])->filter()->implode('_to_');

        return 'govassist-'.$name.($suffix !== '' ? '-'.$suffix : '').'-'.now()->format('Ymd-His').'.xlsx';
    }

    private function date(?CarbonInterface $date): ?string
    {
        return $date?->format('Y-m-d H:i');
    }
}
