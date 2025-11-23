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
        // 1. Limpiar duplicados - mantener solo el recovery_email del usuario más antiguo
        DB::statement('
            UPDATE users u1
            LEFT JOIN (
                SELECT MIN(id) as min_id, recovery_email
                FROM users
                WHERE recovery_email IS NOT NULL
                GROUP BY recovery_email
            ) u2 ON u1.recovery_email = u2.recovery_email AND u1.id = u2.min_id
            SET u1.recovery_email = NULL,
                u1.recovery_email_verified_at = NULL,
                u1.recovery_verification_code = NULL,
                u1.recovery_code_expires_at = NULL
            WHERE u1.recovery_email IS NOT NULL
            AND u2.min_id IS NULL
        ');

        // 2. Renombrar las columnas
        DB::statement('ALTER TABLE users RENAME COLUMN recovery_email TO secondary_email');
        DB::statement('ALTER TABLE users RENAME COLUMN recovery_email_verified_at TO secondary_email_verified_at');
        DB::statement('ALTER TABLE users RENAME COLUMN recovery_verification_code TO secondary_email_verification_code');
        DB::statement('ALTER TABLE users RENAME COLUMN recovery_code_expires_at TO secondary_email_code_expires_at');

        // 3. Agregar índice único al secondary_email
        Schema::table('users', function (Blueprint $table) {
            $table->unique('secondary_email', 'users_secondary_email_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique('users_secondary_email_unique');
        });

        // Revertir los nombres de las columnas
        DB::statement('ALTER TABLE users RENAME COLUMN secondary_email TO recovery_email');
        DB::statement('ALTER TABLE users RENAME COLUMN secondary_email_verified_at TO recovery_email_verified_at');
        DB::statement('ALTER TABLE users RENAME COLUMN secondary_email_verification_code TO recovery_verification_code');
        DB::statement('ALTER TABLE users RENAME COLUMN secondary_email_code_expires_at TO recovery_code_expires_at');
    }
};
