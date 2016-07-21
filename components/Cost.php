<?php namespace Octommerce\Shipping\Components;

use Db;
use ApplicationException;
use Cms\Classes\ComponentBase;
use RainLab\Location\Models\State;
use Octommerce\Shipping\Models\City;
use Octommerce\Shipping\Models\Courier;
use Octommerce\Shipping\Models\Cost as CostModel;
use Octommerce\Shipping\Models\Settings;
use Octommerce\Shipping\Models\Package;

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
        $data = post();
        $weight = $data['weight'];
        $shippingCost = 'Free';
        $cityOriginId = Settings::get('city_id');

        $query = CostModel::query()
            ->where('city_origin_id', $cityOriginId)
            ->where('city_destination_id', $data['city_id'])
            ->where('package_id', $data['package_id']);

        $cost = $query->first();

        if (! $cost) {
            throw new ApplicationException('Shipping cost not found!');
        }

        $this->page['is_insurance'] = Package::find($data['package_id'])->is_insurance;

        if ($cost->is_per_kg) {
            // Flat cost
            if ($cost->min == 0 && $cost->max == 0) {
                $shippingCost = $weight * $cost->amount; 
            }  
            // Dynamic cost
            else {
                $cost = $query->select(Db::raw('SUM(CASE 
                    WHEN '. $weight .' < max THEN amount * ('. $weight .' - min + 1) 
                    WHEN '. $weight .' > max AND min = 0 THEN amount * (max - min)
                    WHEN '. $weight .' > max THEN amount * (max - min + 1)
                    END) AS total'))
                    ->first();

                $shippingCost = $cost ? $cost->total : $shippingCost;
            }
        }
        // Range cost
        else {
            $cost = $query->where('min', '<=', $weight)->where('max', '>=', $weight)->first();

            $shippingCost = $cost ? $cost->amount : $shippingCost; 
        }

        return ['#shippingCost' => $shippingCost];
    }

    function onSelectState() {
        $this->page['cities'] = State::find(post('state_id'))->cities;
    }

    function onSelectCourier() {
        $this->page['packages'] = Courier::find(post('courier_id'))->packages;
    }

    /**
     * AJAX handler when selecting insurance
     *
     * @param $packageId
     * @param $subtotal 
     *
     * @return array
     **/
    protected function onSelectInsurance()
    {
        $data = post();
        $insuranceFee = 'Not available';

        $package = Package::find($data['package_id']);

        if (! $data['is_insurance']) {
            return ['#insuranceFee' => '-'];
        }

        if ($package->is_insurance) {
            $this->page['is_insurance'] = $package->is_insurance;
        
            if ($package->is_fixed) {
                $insuranceFee =  $package->amount; 
            }
            else {
                $insuranceFee = $data['subtotal'] * $package->amount / 100;
            }
        }

        return ['#insuranceFee' => $insuranceFee];
    }
}
