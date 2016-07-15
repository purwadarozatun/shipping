<?php namespace Octommerce\Shipping\Components;

use Cms\Classes\ComponentBase;
use RainLab\Location\Models\State;
use Octommerce\Shipping\Models\City;
use Octommerce\Shipping\Models\Courier;
use Octommerce\Shipping\Models\Cost as CostModel;

class Cost extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name'        => 'Cost Component',
            'description' => 'Get shipping cost by selected courier and package.'
        ];
    }

    public function defineProperties()
    {
        return [];
    }

    /**
     * Retrieve all states
     * 
     * @return Collection
     **/
    public function getAllStates() {
        return State::whereHas('country', function($query) {
            $query->whereName('Indonesia');
        })->get();
    }

    /**
     * Retrieve all cities.
     *
     * @return Collection
     */
    public function getAllCities()
    {
        return City::orderBy('name', 'ASC')->get();
    }

    /**
     * Retrieve all couriers.
     *
     * @return Collection
     */
    public function getAllCouriers()
    {
        return Courier::orderBy('name', 'ASC')->get();
    }

    /**
     * Calculate the shipping cost
     *
     * @return array
     */
    public function onSelectCourierPackage()
    {
        /** 
         * TODO
         * Get default / origin city
         * Get products weight
         * Improve shipping cost by each rule (Flat, dynamic and range cost)
         */
        $data = post();
        $weight = 1;
        $shippingCost = 'Free';

        $cost = CostModel::where('city_origin_id', 1)
            ->where('city_destination_id', $data['city_id'])
            ->where('package_id', $data['package_id'])
            ->first();

        if (! $cost) {
            throw new ApplicationException('Shipping cost not found!');
        }

        if ($cost->is_per_kg) {
            // Flat cost
            if ($cost->min == 0 && $cost->max == 0) {
                $shippingCost = $weight * $cost->amount; 
            }  
            // Dynamic cost
            else {
            
            }
        }
        // Range cost
        else {
        
        }

        return ['#shippingCost' => $shippingCost];
    }

    function onSelectState() {
        $this->page['cities'] = State::find(post('state_id'))->cities;
    }

    function onSelectCourier() {
        $this->page['packages'] = Courier::find(post('courier_id'))->packages;
    }
}
