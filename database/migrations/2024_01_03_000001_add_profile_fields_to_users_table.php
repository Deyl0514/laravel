<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'gender')) {
                $table->string('gender')->nullable()->after('password');
            }
            if (! Schema::hasColumn('users', 'address')) {
                $table->text('address')->nullable()->after('gender');
            }
            if (! Schema::hasColumn('users', 'profile_picture')) {
                $table->string('profile_picture')->nullable()->after('address');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = array_filter(
                ['profile_picture', 'address', 'gender'],
                fn ($c) => Schema::hasColumn('users', $c)
            );
            if ($columns) {
                $table->dropColumn($columns);
            }
        });
    }
};
