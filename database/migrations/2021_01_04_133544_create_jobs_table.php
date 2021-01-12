<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('file_url')->nullable()->default(null);
            $table->string('name')->nullable()->default(null);
            $table->unsignedInteger('time_in_seconds')->nullable()->default(null);
            $table->timestamp('start_at')->nullable()->default(null);
            $table->unsignedInteger('uploader_id')->nullable()->default(null);
            $table->unsignedInteger('collection_id')->nullable()->default(null);
            $table->unsignedInteger('worker_id')->nullable()->default(null);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jobs');
    }
}
