<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `users` MODIFY `role` VARCHAR(32) NOT NULL DEFAULT 'bendahara'");
        } elseif (Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('role', 32)->default('bendahara')->change();
            });
        }
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::table('users')->where('role', 'super_admin')->update(['role' => 'admin']);
            DB::statement("ALTER TABLE `users` MODIFY `role` ENUM('admin', 'bendahara') NOT NULL DEFAULT 'bendahara'");
        }
    }
};
