<?php namespace Octommerce\Shipping\Models;

use Model;

/**
 * Cost Model
 */
class Cost extends Model
{

    /**
     * @var string The database table used by the model.
     */
    public $table = 'octommerce_shipping_costs';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [];

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [];
    public $belongsTo = [
        'origin_city' => [
            'Octommerce\Shipping\Models\City',
            'key'      => 'city_origin_id',
            'otherKey' => 'id'
        ],
        'destination_city' => [
            'Octommerce\Shipping\Models\City',
            'key'      => 'city_destination_id',
            'otherKey' => 'id'
        ],
        'courier' => [
            'Octommerce\Shipping\Models\Courier',
            'key'      => 'courier_id',
            'otherKey' => 'id'
        ],
        'package' => [
            'Octommerce\Shipping\Models\Package',
            'key'      => 'package_id',
            'otherKey' => 'id'
        ]
    ];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

}
