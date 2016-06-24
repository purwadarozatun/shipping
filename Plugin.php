<?php namespace Octommerce\Shipping;

use Backend;
use System\Classes\PluginBase;

/**
 * Shipping Plugin Information File
 */
class Plugin extends PluginBase
{

    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'Shipping',
            'description' => 'No description provided yet...',
            'author'      => 'Octommerce',
            'icon'        => 'icon-leaf'
        ];
    }

    /**
     * Register method, called when the plugin is first registered.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Boot method, called right before the request route.
     *
     * @return array
     */
    public function boot()
    {

    }

    /**
     * Registers any front-end components implemented in this plugin.
     *
     * @return array
     */
    public function registerComponents()
    {
        return []; // Remove this line to activate

        return [
            'Octommerce\Shipping\Components\MyComponent' => 'myComponent',
        ];
    }

    /**
     * Registers any back-end permissions used by this plugin.
     *
     * @return array
     */
    public function registerPermissions()
    {
        return []; // Remove this line to activate

        return [
            'octommerce.shipping.some_permission' => [
                'tab' => 'Shipping',
                'label' => 'Some permission'
            ],
        ];
    }

    /**
     * Registers back-end navigation items for this plugin.
     *
     * @return array
     */
    public function registerNavigation()
    {
        return []; // Remove this line to activate

        return [
            'shipping' => [
                'label'       => 'Shipping',
                'url'         => Backend::url('octommerce/shipping/mycontroller'),
                'icon'        => 'icon-leaf',
                'permissions' => ['octommerce.shipping.*'],
                'order'       => 500,
            ],
        ];
    }

}
