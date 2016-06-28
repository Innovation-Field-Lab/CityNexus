<?php


namespace CityNexus\CityNexus;


use Carbon\Carbon;
use CityNexus\CityNexus;
use Geocoder\Geocoder;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use CityNexus\CityNexus\GeocodeJob;
use CityNexus\CityNexus\Location;
use CityNexus\CityNexus\Property;

class TableBuilder
{
    public function create($table)
    {
        $table_name = 'tabler_' . $this->cleanName($table->table_title);

        $fields = json_decode($table->scheme);

        if(!Schema::hasTable($table_name)) {
            Schema::create($table_name, function (Blueprint $table) use ($fields) {
                // Create table's index id file
                $table->increments('id');
                $table->integer('upload_id');

                // Add another index field if one is set in the config file.
                if (config('citynexus.index_id') != null && config('citynexus.index_id') != 'id') {
                    $table->integer(config('citynexus.index_id'))->nullable();
                }

                foreach ($fields as $field) {
                    $type = $field->type;
                    if($field->key == 'id') {$field->key = $field->key . '-original';}
                    $table->$type($field->key)->nullable();
                }
                $table->json('raw')->nullable();
                $table->dateTime('processed_at')->nullable();
                $table->timestamps();
            });
        }

        return $table_name;
    }

    public function cleanName($name)
    {
        return strtolower(str_replace(' ', '_', (preg_replace('/[^a-zA-Z0-9_ -%][().\'][\/]/s', '', $name))));
    }



    /**
     * @param $scheme
     * @return array
     */
    public function findValues( $scheme, $type )
    {
        $results = [];

        // Decode Scheme
        $scheme = json_decode($scheme, true);
        foreach ($scheme as $key => $i) {
            if (isset($i[$type]) && $i[$type] != null) {
                $results[$key] = $i[$type];
            }
        }
        return $results;
    }

    /**
     * @param $table_name
     * @param $i
     * @param $syncValues
     * @return mixed
     */
    public function findSyncId( $i, $syncValues)
    {
        $search = [];

        foreach($syncValues as $key => $field)
        {
            if($key != null && isset($i->$key)) $search[$field] = $i->$key;
        }

        $search = $this->processAddress($search);

        if($search)
        {

        //Get the ID of the first row matching the sync parameters
        $return = Property::where($search)->pluck('id');

        // Run Geocode of Address
        if($return == null) {

            $curl = new \Ivory\HttpAdapter\CurlHttpAdapter();
            $geocoder = new \Geocoder\Provider\GoogleMaps($curl);

            $geocode = $geocoder->geocode($search['full_address'] . ', ' . config('citynexus.city_state'));
            $geocode = $geocode->first();
            $full_address = trim($geocode->getStreetNumber() . ' ' . $geocode->getStreetName() . ' ' . $search['unit']);

            $return = \CityNexus\CityNexus\Property::where('full_address', $full_address)->pluck('id');

            if($return == null)
            {
                // Check if should be flagged
                $search = $this->checkAddress($search);

                // Create Location
                $location = new Location;
                $location->lat = $geocode->getLatitude();
                $location->long = $geocode->getLongitude();
                $location->street_number = $geocode->getStreetNumber();
                $location->street_name = $geocode->getStreetName();
                $location->polygon = json_encode($geocode->getBounds());
                $location->locality = $geocode->getLocality();
                $location->postal_code = $geocode->getPostalCode();
                $location->sub_locality = $geocode->getSubLocality();
                $location->admin_levels = json_encode($geocode->getAdminLevels());
                $location->country = $geocode->getCountry();
                $location->country_code = $geocode->getCountryCode();
                $location->timezone = $geocode->getTimezone();
                $location->raw = json_encode($geocode);
                $location->address = $location->street_number . ' ' . $location->street_name;
                $location->save();

                $new = new Property;

                $new->full_address = $full_address;
                $new->house_number = $location->street_number;
                $new->street_name = $location->street_name;
                $new->unit = $search['unit'];
                $new->city = $location->locality;
                $new->state =
                $new->zip = $location->postal_code;
                $new->location_id = $location->id;

                $new->save();

                // Create a new record in the master table.
                $return = $new->id;
            }


        }

        return $return;

        }
        else
            return false;
    }

    protected function checkAddress($search)
    {

        // Check if there are more than three addresses on the street
        if(Property::where('street_name', $search['street_name'])
                ->where('street_type', $search['street_type'])
                ->count() < 1)
        {
            $search['review'] = true;
        }

        //Check if there are more than a single unit in the building
        if(Property::where('street_name', $search['street_name'])
                ->where('street_type', $search['street_type'])
                ->where('house_number', $search['house_number'])
                ->where('unit', '!=', 'null')
                ->count() < 1)
        {
            $search['review'] = true;
        }

//        if($search['street_name'] == null && $search['house_number'] == null)
//        {
//            throw new \Exception('No meaningful address');
//        }

        return $search;
    }

    /**
     * @param $input
     */
    protected function processAddress($input)
    {
        $return = ['full_address' => null, 'house_number' => null, 'street_name' => null, 'street_type' => null, 'unit' => null];

        $streets = config('citynexus.street_types');

        $units = config('citynexus.unit_types');

        $parts = null;


        // Find house numbers with apt number embedded
        if(array_key_exists('house_number', $input) && strpos($input['house_number'], '#') != null)
        {
            $elements = explode('#', $input['house_number']);
            $return['house_number'] = trim($elements[0]);
            $return['unit'] = '#' . $elements[1];
        }

        if(array_key_exists('house_number', $input) && array_key_exists('street_name', $input))
        {
            if($return['house_number'] == null){
                $return['house_number'] = $input['house_number'];
            }
            $street_name = $input['street_name'];
            $street_name = preg_replace('/[^\p{L}\p{N}\s]/u', '', $street_name);
            $street_name = strtolower($street_name);
            $parts = explode(' ', $street_name);
        }

        //If full address is provided
        if(array_key_exists('full_address', $input))
        {
            $full_address = $input['full_address'];
            $full_address = preg_replace('/[^\p{L}\p{N}\s]/u', '', $full_address);
            $full_address = strtolower($full_address);
            //break up into array
            $parts = explode(' ', $full_address);
            //Use first element as street number
            $return['house_number'] = $parts[0];
            unset($parts[0]);
        }

        $unit = false;
        if($parts != null)
        {
        foreach($parts as $k => $i)
        {
            if(array_key_exists($i, $units) or is_integer($i) && $return['unit'] == null)
            {
                $unit = true;

                $return['unit'] = $return['unit'] . ' ' . $units[$i];
            }
            elseif(array_key_exists($i, $streets))
            {
                $return['street_type'] = trim($return['street_type'] . ' ' . $streets[$i]);

                $unit = true;
            }
            else
            {
                if($unit && !isset($input['unit']))
                {
                    $return['unit'] = trim($return['unit'] . ' ' . $i);
                }
                else
                {
                    $return['street_name'] = trim($return['street_name'] . ' ' . $i);
                }
            }

            unset($parts[$k]);
        }

        if(array_key_exists('unit', $input)) $return['unit'] = trim($input['unit']);
        if(array_key_exists('street_type', $input)) $return['street_type'] = trim($input['street_type']);

        //Clear stray characters of unit variable.
        if($return['unit'] == null) $return['unit'] = null;

        $return['full_address'] = trim(trim($return['house_number']) . ' ' . trim($return['street_name']) . ' ' . trim($return['street_type']) . ' ' . $return['unit']);

            return $return;
        }
        else

            return false;

    }

    /**
     * @param $house_number
     * @param $street_name
     * @param null $unit
     * @return string
     */
    protected function createFullAddress($house_number, $street_name, $unit = null)
    {
        return trim($house_number . ' ' . $street_name . ' ' . $unit);
    }

    /**
     * @param $record
     * @param $map
     * @param $data
     * @return mixed
     */
    public function addElements( $record, $data, $map)
    {
        foreach($map as $key => $i)
        {
           if(isset($data->$key)) {
               $record[$i->key] = $data->$key;
           }
            $record['created_at'] = Carbon::today();
            $record['updated_at'] = Carbon::today();
        }

        return $record;
    }

    protected function censusLookUp( $search )
    {

        $address = $search['full_address'] . " chelsea, ma";
        $address = urlencode($address);

        $address = "http://geocoding.geo.census.gov/geocoder/locations/onelineaddress?address=" . $address . "&benchmark=9&format=json";

        // create curl resource
        $ch = curl_init();

        // set url
        curl_setopt($ch, CURLOPT_URL, $address);

        //return the transfer as a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // $output contains the output string
        $output = curl_exec($ch);

        // close curl resource to free up system resources
        curl_close($ch);

        return json_decode($output)->result->addressMatches;

    }

    public function processRecord($id, $table)
    {

        //Create a empty array of the record
        $record = [];

        $data = json_decode($data = DB::table($table->table_name)->where('id', $id)->pluck('raw'));
        $settings = \GuzzleHttp\json_decode($table->settings);

        $tabler = new TableBuilder();

        //create an array of sync values
        $syncValues = $tabler->findValues( $table->scheme, 'sync' );
        $pushValues = $tabler->findValues( $table->scheme, 'push' );

        $scheme = json_decode($table->scheme);

        //if there is a sync value, identify the index id
        if(isset($settings->property_id) && $settings->property_id)
        {
            $record[config('citynexus.index_id')] = $data->$settings->property_id;
        }
        elseif( count( $syncValues ) > 0)
        {
            $record[config('citynexus.index_id')] = $this->findSyncId($data, $syncValues);
        }

        if(isset($record[config('citynexus.index_id')]))
        {

            //add remaining elements to the array
            $record = $this->addElements($record, $data, $scheme);
            $record = $this->processElements($scheme, $record);
            $record = $this->checkForUsedKeys($record, $table);

            if (isset($settings->timestamp) && $settings->timestamp != null) {
                $record['created_at'] = $record[$settings->timestamp];
            }

            try{

                DB::table($table->table_name)->where('id', $id)->update($record);

            }
            catch(\Exception $e)
            {
                Error::create(['location' => 'processRecord - Insert Record', 'data' => json_encode(['e' => $e, 'id' => $id, 'table' => $table, 'record' => $record])]);
            }
            //If there are push values, update the primary property record
            if (count($pushValues) > 0) {
                $property = Property::find($record['property_id']);
                foreach ($pushValues as $key => $value) {
                    $property->$value = $data[$key];
                }
                $property->save();
            }

            return $record['property_id'];
        } else
        {
            return false;
        }

    }

    /**
     *
     * Check that the array doesn't contain any
     * of the same keys as CityNexus uses
     *
     * @param $records array
     * @return array
     */
    private function checkForUsedKeys($records, $table)
    {
        $settings = \GuzzleHttp\json_decode($table->settings);

        $keys = ['id', 'property_id', 'upload_id', 'updated_at', 'raw', 'created_at'];
        foreach($keys as $i) {
            if(isset($settings->$i) && $settings->$i == null | isset($settings->$i) && $settings->$i != $i)
                if(isset($record[$i])) {
                $record[$i . '-original'] = $record[$i];
                unset($record[$i]);
            }
        }

        return $records;
    }

    private function processElements($scheme, $record)
    {
        foreach ($scheme as $field) {
            if ($field->type == 'float') {
                if (array_key_exists($field->key, $record)) $record[$field->key] = floatval(preg_replace("/[^0-9,.]/", "", $record[$field->key]));
            }
            elseif($field->type == 'integer')
            {
                if (array_key_exists($field->key, $record)) $record[$field->key] = intval(preg_replace("/[^0-9,.]/", "", $record[$field->key]));
            }
            elseif ($field->type == 'datetime') {

                if (array_key_exists($field->key, $record))
                {
                    if(is_array($record[$field->key]))
                    {
                        $record[$field->key] = Carbon::createFromTimestamp(strtotime($record[$field->key]['date']));

                    }
                    else
                    {
                        $record[$field->key] = Carbon::createFromTimestamp(strtotime($record[$field->key]));
                    }
                }
            }
        }

        return $record;
    }

}