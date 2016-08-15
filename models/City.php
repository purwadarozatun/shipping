<?php namespace Octommerce\Shipping\Models;

use Model;

/**
 * City Model
 */
class City extends Model
{

    /**
     * @var string The database table used by the model.
     */
    public $table = 'octommerce_shipping_cities';

    /**
     * Softly implement the CityModel behavior.
     **/
    public $implement = ['@Octommerce.Octommerce.Behaviors.CityModel']; 

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = ['name'];

    /**
     * @var array Relations
     */
    public $belongsTo = [
        'state' => 'RainLab\Location\Models\State',
    ];
    public $hasMany = [
        'costs'  => [
            'Octommerce\Shipping\Models\Cost',
            'key' => 'city_origin_id',
        ]
    ];

}
