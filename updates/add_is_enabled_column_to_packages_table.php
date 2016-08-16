<?php namespace Octommerce\Shipping\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class AddIsEnabledColumnToPackagesTable extends Migration
{
    public function up()
    {
        Schema::table('octommerce_shipping_packages', function(Blueprint $table) {
            $table->boolean('is_enabled')->default(true)->after('name');
        });
    }

    public function down()
    {
        Schema::table('octommerce_shipping_packages', function(Blueprint $table) {
            $table->dropColumn('is_enabled');
        });

    }
}
