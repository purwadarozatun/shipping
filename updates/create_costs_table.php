<?php namespace Octommerce\Shipping\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreateCostsTable extends Migration
{
    public function up()
    {
        Schema::create('octommerce_shipping_costs', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('city_origin_id')->unsigned()->nullable();
            $table->integer('city_destination_id')->unsigned()->nullable();
            $table->integer('courier_id')->unsigned()->nullable();
            $table->integer('package_id')->unsigned()->nullable();
            $table->decimal('amount', 10, 2)->unsigned()->default(0);
            $table->boolean('is_per_kg')->default(0);
            $table->integer('min')->unsigned()->default(0);
            $table->integer('max')->unsigned()->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('octommerce_shipping_costs');
    }
}
