<?php namespace Octommerce\Shipping\Models;

use Model;

/**
 * Settings Model
 */
class Settings extends Model
{
    public $implement = ['System.Behaviors.SettingsModel'];

    public $settingsCode = 'octommerce_shipping_settings';

    public $settingsFields = 'fields.yaml';

    public $belongsTo = [
        'city' => 'Octommerce\Shipping\Models\City' 
    ];

}
