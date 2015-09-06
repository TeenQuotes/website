<?php

/*
 * This file is part of the Teen Quotes website.
 *
 * (c) Antoine Augusti <antoine.augusti@teen-quotes.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateQuotesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::dropIfExists('quotes');

        Schema::create('quotes', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('content', 500);
            $table->integer('user_id')->unsigned()->index();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->tinyInteger('approved')->default(0);
            $table->timestamps();
        });

        if (App::environment() != 'testing') {
            DB::statement('ALTER TABLE quotes ADD FULLTEXT search(content)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('quotes', function ($table) {
            $table->dropIndex('search');
        });
        Schema::drop('quotes');
    }
}
