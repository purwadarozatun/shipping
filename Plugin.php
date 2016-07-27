<?php namespace Octommerce\Shipping;

use Backend;
use System\Classes\PluginBase;
use RainLab\Location\Models\State;

/**
 * Shipping Plugin Information File
 */
class Plugin extends PluginBase
{

    public $require = ['RainLab.Location'];

    /**
     * Register method, called when the plugin is first registered.
     *
     * @return void
     */
    public function register()
    {
        $this->registerConsoleCommand('shipping:seed', 'Octommerce\Shipping\Console\Seed');
    }

    /**
     * Boot method, called right before the request route.
     *
     * @return array
     */
    public function boot()
    {
        /**
         * Extend RainLab\Location\Models\State
         */
        State::extend(function($model) {
            $model->hasMany['cities'] = [
                'Octommerce\Shipping\Models\City'
            ];
        });
    }

    /**
     * Registers any front-end components implemented in this plugin.
     *
     * @return array
     */
    public function registerComponents()
    {
        return [
            'Octommerce\Shipping\Components\Cost' => 'shippingCost',
        ];
    }

    public function registerSettings()
    {
        return [
            'config' => [
                'label'       => 'Shipping',
                'icon'        => 'icon-truck',
                'description' => 'Configure Shipping plugins.',
                'class'       => 'Octommerce\Shipping\Models\Settings',
                'permissions' => ['octommerce.shipping.manage_plugins'],
                'order'       => 60
            ]
        ];
    }

}
