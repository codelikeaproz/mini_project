<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // First, create the enum type for user roles (if it doesn't exist)
        DB::statement("DO $$ BEGIN
            CREATE TYPE user_role_enum AS ENUM ('admin', 'mdrrmo_staff');
        EXCEPTION
            WHEN duplicate_object THEN null;
        END $$;");

        Schema::table('users', function (Blueprint $table) {
            // Add MDRRMO-specific user fields first
            $table->string('first_name', 100)->nullable()->after('id');
            $table->string('last_name', 100)->nullable()->after('first_name');
            $table->string('phone_number', 20)->nullable()->after('email_verified_at');
            $table->string('municipality', 100)->default('Maramag')->after('phone_number');
            $table->string('position', 100)->nullable()->after('municipality');
            $table->boolean('is_active')->default(true)->after('position');
            $table->timestamp('last_login_at')->nullable()->after('is_active');
            $table->integer('failed_login_attempts')->default(0)->after('last_login_at');
            $table->timestamp('locked_until')->nullable()->after('failed_login_attempts');

            // Authentication fields for 2FA and email verification
            $table->boolean('is_verified')->default(false)->after('remember_token');
            $table->string('verification_token')->nullable()->after('is_verified');
            $table->string('two_factor_code')->nullable()->after('verification_token');
            $table->timestamp('two_factor_expires_at')->nullable()->after('two_factor_code');
            $table->string('avatar')->nullable()->after('two_factor_expires_at');
        });

        // Migrate data from 'name' column to 'first_name' and 'last_name' if name column exists
        if (Schema::hasColumn('users', 'name')) {
            // Split existing name data into first_name and last_name
            DB::statement("
                UPDATE users
                SET
                    first_name = CASE
                        WHEN name IS NOT NULL AND position(' ' in name) > 0
                        THEN split_part(name, ' ', 1)
                        ELSE COALESCE(name, 'Unknown')
                    END,
                    last_name = CASE
                        WHEN name IS NOT NULL AND position(' ' in name) > 0
                        THEN substring(name from position(' ' in name) + 1)
                        ELSE 'User'
                    END
                WHERE first_name IS NULL OR last_name IS NULL
            ");

            // Now drop the name column
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('name');
            });
        }

        // Make first_name and last_name NOT NULL after data migration
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name', 100)->nullable(false)->change();
            $table->string('last_name', 100)->nullable(false)->change();
        });

        // Add the role column using the enum type
        DB::statement("ALTER TABLE users ADD COLUMN role user_role_enum NOT NULL DEFAULT 'mdrrmo_staff'");

        // Create indexes for performance
        DB::statement('CREATE INDEX IF NOT EXISTS idx_users_email ON users(email)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_users_role ON users(role)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_users_municipality ON users(municipality)');
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add back the name column as nullable first
            $table->string('name')->nullable()->after('id');
        });

        // Migrate data back from first_name and last_name to name
        DB::statement("
            UPDATE users
            SET name = CONCAT(first_name, ' ', last_name)
            WHERE first_name IS NOT NULL AND last_name IS NOT NULL
        ");

        Schema::table('users', function (Blueprint $table) {
            // Make name column NOT NULL after data migration
            $table->string('name')->nullable(false)->change();

            // Remove MDRRMO-specific columns
            $table->dropColumn([
                'first_name', 'last_name', 'phone_number', 'municipality', 'position',
                'is_active', 'last_login_at', 'failed_login_attempts', 'locked_until',
                'is_verified', 'verification_token', 'two_factor_code',
                'two_factor_expires_at', 'avatar'
            ]);
        });

        // Drop the role column and enum type
        DB::statement('ALTER TABLE users DROP COLUMN role');
        DB::statement('DROP TYPE IF EXISTS user_role_enum');

        // Drop indexes
        DB::statement('DROP INDEX IF EXISTS idx_users_email');
        DB::statement('DROP INDEX IF EXISTS idx_users_role');
        DB::statement('DROP INDEX IF EXISTS idx_users_municipality');
    }
};
