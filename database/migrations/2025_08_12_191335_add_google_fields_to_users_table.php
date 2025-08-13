<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('users', function (Blueprint $table) {
        $table->string('google_id')->nullable()->unique();
        $table->text('google_token')->nullable();
        $table->text('google_refresh_token')->nullable();
        $table->timestamp('google_token_expires_at')->nullable();
    });
}

public function down()
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn(['google_id','google_token','google_refresh_token','google_token_expires_at']);
    });
}

};
