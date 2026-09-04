<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('user_inquiries', function (Blueprint $table) {
            if (! Schema::hasColumn('user_inquiries', 'ticket_number')) {
                $table->string('ticket_number')->nullable()->unique()->after('id');
            }
            if (! Schema::hasColumn('user_inquiries', 'subject')) {
                $table->string('subject')->nullable()->after('service_id');
            }
        });

        // Backfill existing inquiries with ticket numbers if null
        $inquiries = DB::table('user_inquiries')->whereNull('ticket_number')->get();
        foreach ($inquiries as $inquiry) {
            $ticketNumber = 'TKT-'.date('Y', strtotime($inquiry->created_at)).'-'.str_pad((string) $inquiry->id, 5, '0', STR_PAD_LEFT);
            DB::table('user_inquiries')->where('id', $inquiry->id)->update([
                'ticket_number' => $ticketNumber,
                'subject' => $inquiry->subject ?: 'Inquiry #'.$inquiry->id,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_inquiries', function (Blueprint $table) {
            if (Schema::hasColumn('user_inquiries', 'ticket_number')) {
                $table->dropColumn('ticket_number');
            }
            if (Schema::hasColumn('user_inquiries', 'subject')) {
                $table->dropColumn('subject');
            }
        });
    }
};
