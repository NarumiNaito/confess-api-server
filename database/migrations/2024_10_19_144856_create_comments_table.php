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
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->comment('ユーザーID');
            $table->foreignId('post_id')->constrained('posts')->onDelete('cascade')->comment('ポストID');
            $table->text('content')->comment('内容');
            $table->timestamps();

            $table->dropForeign('comments_post_id_foreign');
            
        });
    }

    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
