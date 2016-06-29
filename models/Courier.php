<?php namespace Octommerce\Shipping\Models;

use Model;

/**
 * Courier Model
 */
class Courier extends Model
{

    /**
     * @var string The database table used by the model.
     */
    public $table = 'octommerce_shipping_couriers';

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
        'packages' => ['Octommerce\Shipping\Models\Package'],
    ];
    public $belongsTo = [];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

}
