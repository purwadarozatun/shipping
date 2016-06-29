<?php namespace Octommerce\Shipping\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreateCouriersTable extends Migration
{
    public function up()
    {
        Schema::create('octommerce_shipping_couriers', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name');
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('octommerce_shipping_couriers');
    }
}
