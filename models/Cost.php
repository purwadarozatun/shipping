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
    public $belongsTo = [];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

}