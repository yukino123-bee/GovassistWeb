<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Merge duplicate inquiries for registered users
        $userIds = DB::table('user_inquiries')
            ->whereNotNull('user_id')
            ->groupBy('user_id')
            ->pluck('user_id');

        foreach ($userIds as $userId) {
            $inquiries = DB::table('user_inquiries')
                ->where('user_id', $userId)
                ->orderBy('created_at', 'asc')
                ->get();

            if ($inquiries->count() > 1) {
                $master = $inquiries->first();
                $duplicates = $inquiries->slice(1);

                foreach ($duplicates as $dup) {
                    // Ensure the initial inquiry_text is saved as a message in the master thread if missing
                    $exists = DB::table('inquiry_requirenses')
                        ->where('inquiry_id', $master->id)
                        ->where('requireent_text', $dup->inquiry_text)
                        ->exists();

                    if (! $exists && ! empty($dup->inquiry_text)) {
                        DB::table('inquiry_requirenses')->insert([
                            'inquiry_id' => $master->id,
                            'requireent_text' => $dup->inquiry_text,
                            'responded_by' => $dup->user_id,
                            'created_at' => $dup->created_at,
                            'updated_at' => $dup->updated_at,
                        ]);
                    }

                    // Move all responses from the duplicate inquiry to the master inquiry
                    DB::table('inquiry_requirenses')
                        ->where('inquiry_id', $dup->id)
                        ->update(['inquiry_id' => $master->id]);

                    // Delete the duplicate inquiry row
                    DB::table('user_inquiries')->where('id', $dup->id)->delete();
                }

                // Update master inquiry timestamp
                DB::table('user_inquiries')
                    ->where('id', $master->id)
                    ->update(['updated_at' => now()]);
            }
        }

        // 2. Merge duplicate inquiries for guest emails
        $guestEmails = DB::table('user_inquiries')
            ->whereNull('user_id')
            ->whereNotNull('guest_email')
            ->groupBy('guest_email')
            ->pluck('guest_email');

        foreach ($guestEmails as $email) {
            $inquiries = DB::table('user_inquiries')
                ->whereNull('user_id')
                ->where('guest_email', $email)
                ->orderBy('created_at', 'asc')
                ->get();

            if ($inquiries->count() > 1) {
                $master = $inquiries->first();
                $duplicates = $inquiries->slice(1);

                foreach ($duplicates as $dup) {
                    $exists = DB::table('inquiry_requirenses')
                        ->where('inquiry_id', $master->id)
                        ->where('requireent_text', $dup->inquiry_text)
                        ->exists();

                    if (! $exists && ! empty($dup->inquiry_text)) {
                        DB::table('inquiry_requirenses')->insert([
                            'inquiry_id' => $master->id,
                            'requireent_text' => $dup->inquiry_text,
                            'responded_by' => null,
                            'created_at' => $dup->created_at,
                            'updated_at' => $dup->updated_at,
                        ]);
                    }

                    DB::table('inquiry_requirenses')
                        ->where('inquiry_id', $dup->id)
                        ->update(['inquiry_id' => $master->id]);

                    DB::table('user_inquiries')->where('id', $dup->id)->delete();
                }

                DB::table('user_inquiries')
                    ->where('id', $master->id)
                    ->update(['updated_at' => now()]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reverse operation needed for merged inquiry cleanup
    }
};
