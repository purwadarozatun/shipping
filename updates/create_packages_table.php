<?php namespace Octommerce\Shipping\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreatePackagesTable extends Migration
{
    public function up()
    {
        Schema::create('octommerce_shipping_packages', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('courier_id')->unsigned()->nullable();
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('etd')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('octommerce_shipping_packages');
    }
}
