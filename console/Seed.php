<?php namespace Octommerce\Shipping\Console;

use Model;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Helper\ProgressBar;
use RainLab\Location\Models\State;
use Octommerce\Shipping\Models\City;
use Octommerce\Shipping\Models\Cost;
use Octommerce\Shipping\Models\Package;
use Octommerce\Shipping\Models\Courier;

class Seed extends Command
{
    /**
     * @var string The console command name.
     */
    protected $name = 'shipping:seed';

    /**
     * @var string The console command description.
     */
    protected $description = 'Seed shipping cost to database';

    protected $courier;

    /**
     * Create a new command instance.
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->courier = Courier::firstOrCreate(['name' => 'JNE']);
    }

    /**
     * Execute the console command.
     * @return void
     */
    public function fire()
    {
        Model::unguard();

        $provinces = $this->getProvinces();

        foreach ($provinces as $province) {

            $cities = $this->getCities($province->province_id);

            foreach($cities as $city) {
                $this->info(strtoupper('SEEDING ' . $city->city_name . " city of " . $province->province . " state"));
                $this->line('');

                $this->saveCost($city);
            }

            $this->line('');
        }

        Model::reguard();
    }

    /**
     * Save cost to database
     *
     * @return void
     */
    protected function saveCost($originCity)
    {
        $provinces = $this->getProvinces();

        $originState = State::updateOrCreate(['name' => $originCity->province], ['code' => $originCity->province_id]);

        $originCityDb = City::firstOrCreate([
            'name'     => $originCity->city_name,
            'code'     => $originCity->postal_code,
            'state_id' => $originState->id
        ]);

        foreach ($provinces as $province) {
            $cities = $this->getCities($province->province_id);

            $destinationState = State::updateOrCreate(['name' => $province->province], ['code' => $province->province_id]);

            // Start the progress bar
            $this->info("Cities in " . $province->province . " state");
            $progressBarTotal = count($cities);
            $progressBar = new ProgressBar($this->output, $progressBarTotal);
            $progressBar->setFormat('debug');
            $progressBar->start();

            foreach($cities as $city) {
                $cost = $this->getCost($originCity->city_id, $city->city_id);

                $destinationCityDb = City::firstOrCreate([
                    'name'     => $city->city_name,
                    'code'     => $city->postal_code,
                    'state_id' => $destinationState->id
                ]);

                foreach($cost[0]->costs as $cost) {
                    if (! $cost)
                        continue 3;

                    $package = Package::firstOrCreate([
                        'courier_id'  => $this->courier->id,
                        'name'        => $cost->service,
                        'description' => $cost->description,
                    ]);

                    $costDb = Cost::updateOrCreate([
                        'city_origin_id'      => $originCityDb->id,
                        'city_destination_id' => $destinationCityDb->id,
                        'package_id'          => $package->id,
                        'is_per_kg'           => 1
                    ],
                    [
                        'amount'              => $cost->cost[0]->value
                    ]);
                }

                $progressBar->advance();
            }

            $this->line('');
        }
    }

    /**
     * Get the console command arguments.
     * @return array
     */
    protected function getArguments()
    {
        return [];
    }

    /**
     * Get the console command options.
     * @return array
     */
    protected function getOptions()
    {
        return [];
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
            $this->error("cURL Error #:" . $err);
            $this->info('Retrying in 5 seconds');
            sleep(5);

            return $this->getProvinces();
        } 
        else {
            return json_decode($response)->rajaongkir->results;
        }
    }

    /**
     * Get cities by given province is_dir(path)
     *
     * @return object
     */
    protected function getCities($provinceId)
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
            $this->error("cURL Error #:" . $err);
            $this->info('Retrying in 5 seconds');
            sleep(5);

            return $this->getCities($provinceId);
        } 
        else {
            return json_decode($response)->rajaongkir->results;
        }
    }

    /**
     * Get shipping cost
     *
     * @return object
     */
    protected function getCost($originCityId, $destinationCityId, $weight = 1000, $courier = 'jne')
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL            => "http://api.rajaongkir.com/starter/cost",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => "",
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => "POST",
            CURLOPT_POSTFIELDS     => "origin=". $originCityId ."&destination=". $destinationCityId ."&weight=". $weight ."&courier=". $courier,
            CURLOPT_HTTPHEADER     => array(
                "content-type: application/x-www-form-urlencoded",
                "key: ". $this->getApiKey()
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            $this->error("cURL Error #:" . $err);
            $this->info('Retrying in 5 seconds');
            sleep(5);

            return $this->getCost($originCityId, $destinationCityId, $weight, $courier);
        } else {
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
