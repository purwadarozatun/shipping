<?php namespace Octommerce\Shipping\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class AddIsInsuranceIsFixedAndAmountColumnsToPackagesTable extends Migration
{
    public function up()
    {
        Schema::table('octommerce_shipping_packages', function(Blueprint $table) {
            $table->boolean('is_insurance')->default(0)->after('name');
            $table->boolean('is_fixed')->default(0)->after('is_insurance');
            $table->decimal('amount', 5, 1)->default(0)->after('is_fixed');
        });
    }

    public function down()
    {
        Schema::table('octommerce_shipping_packages', function(Blueprint $table) {
            $table->dropColumn('is_insurance');
            $table->dropColumn('is_fixed');
            $table->dropColumn('amount');
        });

    }
}
