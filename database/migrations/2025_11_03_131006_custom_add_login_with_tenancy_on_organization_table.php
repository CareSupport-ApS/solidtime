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
            $table->string('domain')->nullable();
            $table->text('entity_id')->nullable();
            $table->text('secret')->nullable();
            $table->text('tenant_id')->nullable();
            $table->string('redirect_path')->nullable();
            $table->string('default_organization_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropColumn(['domain', 'entity_id', 'secret', 'tenant_id', 'redirect_path','default_organization_id']);
        });
    }
};
