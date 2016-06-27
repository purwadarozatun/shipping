<?php namespace Octommerce\Shipping\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreateCitiesTable extends Migration
{
    public function up()
    {
        Schema::create('octommerce_shipping_cities', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('state_id')->unsigned()->nullable();
            $table->string('name');
            $table->string('code')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('octommerce_shipping_cities');
    }
}
