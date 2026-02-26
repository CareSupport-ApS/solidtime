<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->string('domain')->unique()->nullable();
            $table->text('client_id')->nullable();
            $table->text('secret')->nullable();
            $table->text('tenant_id')->nullable();
            $table->string('redirect_path')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropColumn(['domain', 'client_id', 'secret', 'tenant_id', 'redirect_path']);
        });
    }
};
