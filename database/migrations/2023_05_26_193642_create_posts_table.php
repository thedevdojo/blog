<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->string('title');
            $table->text('body')->nullable();
            $table->string('image')->nullable();
            $table->string('slug')->unique();
            $table->string('excerpt')->nullable();
            $table->string('type')->default('post');
            $table->string('status')->default('DRAFT');
            $table->boolean('active')->default(1);
            $table->boolean('featured')->default(0);

            // SEO COLUMNS
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->text('meta_schema')->nullable();
            $table->text('meta_data')->nullable();

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
        Schema::dropIfExists('posts');
    }
}
