<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->after('user_id')
                ->constrained('categories')->nullOnDelete();
            $table->timestamp('completed_at')->nullable()->after('due_date');
            $table->softDeletes();

            $table->index('category_id');
            $table->index('completed_at');
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn(['category_id', 'completed_at']);
            $table->dropSoftDeletes();
        });
    }
};
