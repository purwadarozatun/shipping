<?php namespace Octommerce\Shipping\Models;

use Model;
use Octommerce\Shipping\Models\City;
use RainLab\Location\Models\State;
use RainLab\Location\Models\Country;

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

    public function beforeSave()
    {
        $values = $this->value;
        $cityFromDb = $this->getCityFromDb($values['origin_city']);

        $city = ['city_id' => $cityFromDb->id];

        $this->value = array_merge($values, $city);

        \Artisan::call('shipping:seed', ['--origin-city-id' => $values['origin_city']]);
    }

    public function getCityFromDb($cityId)
    {
        $cityFromApi = $this->getCity($cityId);

        $country = Country::remember(1440)->whereName('Indonesia')->first();
        $state = State::updateOrCreate(['name' => $cityFromApi->province], ['code' => $cityFromApi->province_id, 'country_id' => $country->id]);

        $city = City::firstOrCreate([
            'name'     => $cityFromApi->city_name,
            'code'     => $cityFromApi->postal_code,
            'state_id' => $state->id
        ]);

        return $city;
    }

    public function getOriginStateOptions()
    {
        $provinces = $this->getProvinces();
        $provinceIds = [];
        $provinceNames = [];

        foreach ($provinces as $province) {
            array_push($provinceIds, $province->province_id);
            array_push($provinceNames, $province->province);
        }

        $options = array_combine($provinceIds, $provinceNames);

        return $options;
    }

    public function getOriginCityOptions()
    {
        if (! $this->origin_state) {
            return []; 
        }

        $cities = $this->getCities($this->origin_state);
        $cityIds = [];
        $cityNames = [];

        foreach ($cities as $city) {
            array_push($cityIds, $city->city_id);
            array_push($cityNames, $city->city_name);;
        }

        $options = array_combine($cityIds, $cityNames);

        return $options;
    }

   /**
    * Get available provinces
    *
    * @return object
    */
    protected function getProvinces()
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL            => "http://api.rajaongkir.com/starter/province",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => "",
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => "GET",
            CURLOPT_HTTPHEADER     => array(
                "key: " . $this->getApiKey()
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            sleep(5);

            return $this->getProvinces();
        } 
        else {
            return json_decode($response)->rajaongkir->results;
        }
    }

    /**
     * Get cities by given province_id
     *
     * @return object
     */
    protected function getCities($provinceId = '')
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL            => "http://api.rajaongkir.com/starter/city?province=" . $provinceId,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => "",
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => "GET",
            CURLOPT_HTTPHEADER     => array(
                "key: " . $this->getApiKey()
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            sleep(5);

            return $this->getCities($provinceId);
        } 
        else {
            return json_decode($response)->rajaongkir->results;
        }
    }

    /**
     * Get city by given city_id
     *
     * @return object
     */
    protected function getCity($cityId)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL            => "http://api.rajaongkir.com/starter/city?id=". $cityId ."&province",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => "",
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => "GET",
            CURLOPT_HTTPHEADER     => array(
                "key: " . $this->getApiKey()
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            sleep(5);

            return $this->getCity($cityId);
        } 
        else {
            return json_decode($response)->rajaongkir->results;
        }
    }

    /**
     * Get API key
     *
     * @return string
     */
    protected function getApiKey()
    {
        return env('JNE_API_KEY');
    }

}
