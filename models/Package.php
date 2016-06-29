<?php namespace Octommerce\Shipping\Models;

use Model;

/**
 * Package Model
 */
class Package extends Model
{

    /**
     * @var string The database table used by the model.
     */
    public $table = 'octommerce_shipping_packages';

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
    public $hasMany = [
        'costs'  => [
            'Octommerce\Shipping\Models\Cost',
            'key' => 'package_id',
        ]
    ];
    public $belongsTo = [
        'courier' => ['Octommerce\Shipping\Models\Courier'],
    ];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

}
