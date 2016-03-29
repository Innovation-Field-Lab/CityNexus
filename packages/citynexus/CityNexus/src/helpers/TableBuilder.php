<?php


namespace CityNexus\CityNexus;


use Carbon\Carbon;
use CityNexus\CityNexus;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use League\Flysystem\Exception;

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

                    $table->$type($field->key)->nullable();
                }
                $table->timestamps();
            });
        }

        return $table_name;
    }

    public function cleanName($name)
    {
        return strtolower(str_replace(' ', '_', (preg_replace('/[^a-zA-Z0-9_ -%][().][\/]/s', '', $name))));
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
    public function findSyncId( $table_name, $i, $syncValues)
    {
        $search = [];

        foreach($syncValues as $key => $field)
        {
            if($key != null) $search[$field] = $i[$key];
        }

        $search = $this->processAddress($search);

        //Get the ID of the first row matching the sync parameters
        $return = DB::table($table_name)
            ->where($search)
            ->pluck('id');

        //Create new property record
        if($return == null)
        {

            // Check if should be flagged
            $search = $this->checkAddress($search);

            // Add timestamp fields
            $search['created_at'] = Carbon::now();
            $search['updated_at'] = Carbon::now();

            // Create a new record in the master table.
            $return = DB::table($table_name)
                ->insertGetId($search);
        }
        return $return;
    }

    protected function checkAddress($search)
    {

        // Check if there are more than three addresses on the street
        if(Property::where('street_name', $search['street_name'])
                ->where('street_type', $search['street_type'])
                ->count() < 3)
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

        if(array_key_exists('house_number', $input) && array_key_exists('street_name', $input))
        {
            $return['house_number'] = $input['house_number'];
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

        foreach($parts as $k => $i)
        {
            if(array_key_exists($i, $units) or is_integer($i))
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
        if(array_key_exists('street_type', $input)) $return['unit'] = trim($input['street_type']);


        //Clear stray characters of unit variable.
        if($return['unit'] == null) $return['unit'] = null;

        $return['full_address'] = trim($return['house_number'] . ' ' . $return['street_name'] . ' ' . $return['street_type'] . ' ' . $return['unit']);

        return $return;


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
           if(isset($data[$key])) {
               $record[$i->key] = $data[$key];
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

    public function addRecord($i, $table_id, $upload_id)
    {
        //Create a empty array of the record
        $record = [];

        $tabler = new TableBuilder();

        //get the table
        $table = \CityNexus\CityNexus\Table::find($table_id);

        //create an array of sync values
        $syncValues = $tabler->findValues( $table->scheme, 'sync' );

        $pushValues = $tabler->findValues( $table->scheme, 'push' );

        $scheme = json_decode($table->scheme);

        //if there is a sync value, identify the index id
        if( count( $syncValues ) > 0)
        {
            $record[config('citynexus.index_id')] = $this->findSyncId( config('citynexus.index_table'), $i, $syncValues );
        }

        //add remaining elements to the array
        $record = $this->addElements( $record, $i, $scheme);
        $record['upload_id'] = $upload_id;

        foreach($scheme as $field)
        {
            if($field->type == 'integer' or $field->type == 'float')
            {
                if(array_key_exists($field->key, $record)) $record[$field->key] = floatval(preg_replace("/[^0-9,.]/", "", $record[$field->key]));
            }
            elseif($field->type == 'datetime')
            {
                if(array_key_exists($field->key, $record)) $record[$field->key] = Carbon::createFromTimestamp(strtotime($record[$field->key]));
            }
        }

        if($table->timestamp != null)
        {
            $record['created_at'] = $record[$table->timestamp];
        }

        DB::table($table->table_name)->insertGetId($record);

        //If there are push values, update the primary property record
        if(count( $pushValues) > 0)
        {
            $property = Property::find($record['property_id']);
            foreach ($pushValues as $key => $value)
            {
                $property->$value = $i[$key];
            }
            $property->save();
        }

        return $record['property_id'];
    }
}