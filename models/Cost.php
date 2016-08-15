<?php namespace Octommerce\Shipping\Models;

use Model;
use RainLab\Location\Models\State;
use Octommerce\Shipping\Models\Courier;
use Octommerce\Shipping\Models\Package;
use Octommerce\Shipping\Models\City;

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
    protected $fillable = ['name'];

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

    public function beforeSave()
    {
        $this->prepareBeforeSave();
    }

    /**
     * Preparation before save cost
     *
     * @return void
     */
    public function prepareBeforeSave()
    {
        switch ($this->cost_rules) {
            case 'flat': 
            case 'dynamic': 
                $this->is_per_kg = 1;
                break;
            case 'range': 
                $this->is_per_kg = 0;
                break;
        }
        unset($this->cost_rules);
    }
        
    /**
     * Get the origin state name
     *
     * @param  string  $value
     * @return string
     */
    public function getOriginStateNameAttribute($value)
    {
        $state = State::find($value);

        if (!$state) return;

        return $state->name;
    }

    /**
     * Get the destination state name
     *
     * @param  string  $value
     * @return string
     */
    public function getDestinationStateNameAttribute($value)
    {
        $state = State::find($value);

        if (!$state) return;

        return $state->name;
    }
     /**
     * Get the courier name
     *
     * @param  string  $value
     * @return string
     */
    public function getCourierNameAttribute($value)
    {
        $courier = Courier::find($value);

        if (!$courier) return;

        return $courier->name;
    }

    /**
     * List of dropdown options by given fieldName
     *
     * @return array 
     */
    public function getDropdownOptions($fieldName = null, $keyValue = null)
    {
        if ($fieldName == 'origin_state' || $fieldName == 'destination_state')
            $options = State::orderBy('name', 'asc')->lists('name', 'id');
        else if ($fieldName == 'courier')
            $options = Courier::orderBy('name', 'asc')->lists('name', 'id');
        else if ($fieldName == 'package_id')
            $options = $this->getPackageOptions($this->courier);
        else if ($fieldName == 'city_origin_id')
            $options = $this->getOriginCityOptions($this->origin_state);
        else if ($fieldName == 'city_destination_id')
            $options = $this->getDestinationCityOptions($this->destination_state);
        else
            $options = ['' => '-- none --'];

        return $options;
    }

    /**
     * Get package options by given courierId
     *
     * @param string $courier
     * @return array
     */
    public function getPackageOptions($courierId = null)
    {
        $packageOptions = Package::whereCourierId($courierId)
            ->orderBy('name', 'asc')
            ->lists('name', 'id');

        if (! $packageOptions)
            $packageOptions = ['' => '-- none --'];

        return $packageOptions;
    }

    /**
     * Get origin city options by given origin state's id
     *
     * @param string $originStateId
     * @return array
     */
    public function getOriginCityOptions($originStateId = null)
    {
        $originCityOptions = City::whereStateId($originStateId)
            ->orderBy('name', 'asc')
            ->lists('name', 'id');

        if (! $originCityOptions)
            $originCityOptions = ['' => '-- none --'];

        return $originCityOptions;
    }

    /**
     * Get destination city options by given destination state's id
     *
     * @param string $destinationStateId
     * @return array
     */
    public function getDestinationCityOptions($DestinationStateId = null)
    {
        $DestinationCityOptions = City::whereStateId($DestinationStateId)
            ->orderBy('name', 'asc')
            ->lists('name', 'id');

        if (! $DestinationCityOptions)
            $DestinationCityOptions = ['' => '-- none --'];

        return $DestinationCityOptions;
    }
}
