<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            if (Schema::hasColumn('contacts', 'extra_file') && !Schema::hasColumn('contacts','additional_file')) {
                $table->renameColumn('extra_file', 'additional_file');
            }
        });
    }

    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            if (Schema::hasColumn('contacts', 'additional_file') && !Schema::hasColumn('contacts','extra_file')) {
                $table->renameColumn('additional_file', 'extra_file');
            }
        });
    }
};
