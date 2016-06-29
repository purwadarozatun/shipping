<?php namespace Octommerce\Shipping\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class RemoveCourierIdFromCostsTable extends Migration
{
    public function up()
    {
        Schema::table('octommerce_shipping_costs', function($table) {
            $table->dropColumn('courier_id');
        });
    }

    public function down()
    {
        Schema::table('octommerce_shipping_costs', function($table) {
            $table->integer('courier_id')->unsigned()->nullable();
        });
    }
}
