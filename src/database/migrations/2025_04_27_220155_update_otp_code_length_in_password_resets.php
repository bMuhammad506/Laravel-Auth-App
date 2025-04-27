<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateOtpCodeLengthInPasswordResets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('password_resets', function (Blueprint $table) {
            $table->string('otp_code', 255)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('password_resets', function (Blueprint $table) {
            $table->string('otp_code', 10)->change();
        });
    }
}

