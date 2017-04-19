<?php


namespace CityNexus\CityNexus;


use Carbon\Carbon;
use CityNexus\CityNexus;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use CityNexus\CityNexus\GeocodeJob;

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

        $table->table_name = $table_name;
        $table->save();

        return $table_name;
    }

    public function cleanName($name)
    {
        $return = preg_replace("/[^a-zA-Z0-9_ -%][().'!][\/]/s", '', $name);
        $return = strtolower($return);
        $return = str_replace(["'", "`", "!"], '',$return);
        $return = str_replace(["/", " ", "-"], '_',$return);
        return $return;
    }



    /**
     * @param $scheme
     * @return array
     */
    public function findValues( $scheme, $type )
    {
        $results = [];

        // Decode Scheme
        foreach ($scheme as $key => $i) {
            $value = $i->$type;
            if ($value != 'null')
            {
                $results[$key] = $i->$type;
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

        $sync_id = config('citynexus.index_id');

        if(isset($i->$sync_id))
        {
            return $i->$sync_id;
        }

        foreach($syncValues as $key => $field)
        {
            if($key != null && isset($i->$key)) $search[$field] = $i->$key;
        }

        $PSync = new PropertySync();
        if(array_key_exists('full_address', $search)) {
            return $PSync->addressSync($search['full_address']);
        }
        else {
            return $PSync->addressSync($search);
        }

    }

    protected function checkAddress($search)
    {
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

        // Find house house number with hypen
        if(array_key_exists('house_number', $input) && strpos($input['house_number'], '-') != null)
        {
            $elements = explode('-', $input['house_number']);
            $return['house_number'] = trim($elements[0]);
        }

        //
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
            $address = $input['full_address'];
            $full_address = preg_replace('/[^\p{L}\p{N}\s]/u', '', $address);
            $full_address = strtolower($full_address);
            //break up into array
            $parts = explode(' ', $full_address);
            if(strpos($address, '-') != null)
            {
                $elements = explode('-', $address);
                $return['house_number'] = trim($elements[0]);
                unset($parts[0]);
            }
            //Use first element as street number if the first element is a number
            if($return['house_number'] == null)
            {
                $return['house_number'] = $parts[0];
                unset($parts[0]);
            }

            if(!is_numeric($return['house_number']))
            {
                return false;
            }

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

        if(array_key_exists('unit', $input)) {
            $return['unit'] = trim($input['unit']);
        }
        if(array_key_exists('street_type', $input)) {
            $return['street_type'] = trim($input['street_type']);
        }

            //Clear stray characters of unit variable.
            if($return['unit'] == null)
            {
                $return['unit'] = null;
            }

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
        $dataset = Table::where('table_name', $table)->first();

        $data = json_decode(DB::table($table)->where('id', $id)->pluck('raw'));

        $settings = $dataset->schema;

        $tabler = new TableBuilder();

        //create an array of sync values
        $syncValues = $tabler->findValues( $settings, 'sync' );
//        $pushValues = $tabler->findValues( $settings, 'push' );

        //if there is a sync value, identify the index id
        if(isset($settings->property_id) && $settings->property_id)
        {

            $record[config('citynexus.index_id')] = $data->$settings->property_id;
        }
        elseif( count( $syncValues ) > 0)
        {
            $syncId = $this->findSyncId( config('citynexus.index_table'), $data, $syncValues );

            if($syncId != null)
            {

                $record[config('citynexus.index_id')] = $syncId;
            }

        }

        //add remaining elements to the array
            $record = $this->addElements($record, $data, $settings);
            $record = $this->processElements($settings, $record);
            $record = $this->checkForUsedKeys($record, $dataset);

            if (isset($settings->timestamp) && $settings->timestamp != null) {
                $record['created_at'] = $record[$settings->timestamp];
            }

            try{

                DB::table($table)->where('id', $id)->update($record);
            }
            catch(\Exception $e)
            {
                Error::create(['location' => 'processRecord - Insert Record', 'data' => json_encode([ 'id' => $id, 'table' => $table])]);
            }

    }

    public function processRecordWithoutId($id, $table)
    {
        //Create a empty array of the record
        $record = [];
        $dataset = Table::where('table_name', $table)->first();

        $data = json_decode(DB::table($table)->where('id', $id)->pluck('raw'));
        unset($data->property_id);

        $settings = $dataset->schema;

        $tabler = new TableBuilder();

        //create an array of sync values
        $syncValues = $tabler->findValues( $settings, 'sync' );
//        $pushValues = $tabler->findValues( $settings, 'push' );

        //if there is a sync value, identify the index id
        if(isset($settings->property_id) && $settings->property_id)
        {

            $record[config('citynexus.index_id')] = $data->$settings->property_id;
        }
        elseif( count( $syncValues ) > 0)
        {
            $syncId = $this->findSyncId( config('citynexus.index_table'), $data, $syncValues );

            if($syncId != null)
            {

                $record[config('citynexus.index_id')] = $syncId;
            }

        }

        //add remaining elements to the array
        $record = $this->addElements($record, $data, $settings);
        $record = $this->processElements($settings, $record);
        $record = $this->checkForUsedKeys($record, $dataset);

        if (isset($settings->timestamp) && $settings->timestamp != null) {
            $record['created_at'] = $record[$settings->timestamp];
        }

        try{

            DB::table($table)->where('id', $id)->update($record);
        }
        catch(\Exception $e)
        {
            Error::create(['location' => 'processRecord - Insert Record', 'data' => json_encode([ 'id' => $id, 'table' => $table])]);
        }

    }


    public function saveRawAddress($table, $id)
    {
        //Create a empty array of the record
        $dataset = Table::where('table_name', $table)->first();

        $data = json_decode(DB::table($table)->where('id', $id)->pluck('raw'));

        $settings = $dataset->schema;

        //create an array of sync values
        $syncValues = $this->findValues( $settings, 'sync' );

        $syncValues = array_filter($syncValues);

        foreach($syncValues as $key => $field)
        {
            if($key != null && isset($data->$key)) $address[$field] = $data->$key;
        }

        if(isset($address))
        {
            if(is_array($address))
            {
                $pre = null;

                foreach($address as $k => $i)
                {
                    $post[$k] = str_replace(['.', ','], '', strtolower($i));
                }
            }
            elseif(isset($address))
            {
                $post = $goodUrl = str_replace(['.', ','], '', strtolower($address));
            }

            $raw = RawAddress::firstOrCreate(['address' => json_encode($post)]);
            $raw->property_id = DB::table($table)->where('id', $id)->pluck('property_id');
            $raw->save();

            return 'success';
        }

        return 'fail';

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
        $settings = $table->setting;

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

    public function geocode()
    {

    }

}