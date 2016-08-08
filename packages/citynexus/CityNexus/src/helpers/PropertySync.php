<?php


namespace CityNexus\CityNexus;


use CityNexus\CityNexus\RawAddress;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Toin0u\Geocoder\Facade\Geocoder;

class PropertySync
{

    public function addressSync($raw_address)
    {
        if(!is_array($raw_address))
        {
            $raw_address = ['full_address' => $raw_address];
        }

        if(isset($raw_address['full_address']))
        {

            // check for raw match
            if($this->checkForRawID(json_encode($raw_address)))
            {
                return $this->checkForRawID(json_encode($raw_address));
            }

            // Clean address
            $raw_address = $this->cleanAddress($raw_address);

            //Explode address
            $address = explode(' ', $raw_address['full_address']);

            //Process Address
            $address = $this->processFullAddress($address);
        }
        else
        {
            // check for raw match
            if($this->checkForRawID(json_encode($raw_address)))
            {
                return $this->checkForRawID(json_encode($raw_address));
            }

            // Clean array
            $raw_address = $this->cleanAddress($raw_address);

            // Add house number to array
            $address = [];
            if(isset($raw_address['house_number'])) $address = $this->setHouseNumber($raw_address['house_number']);
            if(isset($raw_address['street_name'])) $address = array_merge($address, $this->processStreetName(explode(' ', $raw_address['street_name'])));
            if(isset($raw_address['street_type'])) {$address['street_type'] = $raw_address['street_type'];}
            if(isset($raw_address['unit']) )  {$address['unit'] = $raw_address['unit'];}
            $raw_address = json_encode($raw_address);
        }

        //If a zero address, return false
        if($address == false)
        {
            return false;
        }

        if(!isset($address['house_number']) or $address['house_number'] == null)
        {
            return false;
        }

            $address = array_filter($address);

        //Check for properties
        $address = array_filter($address);
        $property = Property::firstOrCreate($address);
        $property->full_address = $property->full_address = trim($property->house_number . ' ' . $property->street_name . ' ' . $property->street_type . ' ' . $property->unit);

        $property->save();

        //Save the raw upload
        RawAddress::create(['address' => json_encode($raw_address), 'property_id' => $property->id]);

        if(null == $property->location_id )
        {
            try{
                $location = Location::firstOrCreate(['full_address' => $property->full_address]);
                if(env('APP_ENV') != 'testing')
                {
                    $geocode = Geocoder::geocode(   $location->full_address  . ', ' . config('citynexus.city_state'));
                    $location->lat = $geocode->getLatitude();
                    $location->long = $geocode->getLongitude();
                    $location->polygon = \GuzzleHttp\json_encode($geocode->getBounds());
                    $location->street_number = $geocode->getStreetNumber();
                    $location->street_name = $geocode->getStreetName();
                    $location->locality = $geocode->getCity();
                    $location->postal_code = $geocode->getZipcode();
                    $location->sub_locality = $geocode->getRegion();
                    $location->country = $geocode->getCountry();
                    $location->country_code = $geocode->getCountryCode();
                    $location->timezone = $geocode->getTimezone();
                }
                $location->save();
                $property->location_id = $location->id;
                $property->save();
            }
            catch(\Exception $e)
            {
                Error::create(['location' => 'geocode', 'data' => json_encode(['property_id' => $property->id])]);
                return $property->id;
            }
        }
        return $property->id;
    }

    private function processFullAddress($address)
    {
        //Set House Number
        $property = $this->setHouseNumber($address[0]);
        unset($address[0]);
        $address = array_values($address);

        //Finish processing address into elements
        $processed_address = $this->processStreetName($address);
        $property = array_merge($property, $processed_address);

        return $property;
    }

    /**
     *
     * Clean Address
     *
     * Take raw address upload and return a
     * lightly cleaned address for matching
     *
     * @param $address
     */
    private function cleanAddress($address)
    {
        $post = null;

        foreach($address as $k => $i)
        {
            $post[$k] = str_replace(['.', ','], '', strtolower($i));
        }

        return $post;
    }

    /**
     *
     * Check for Raw ID searches for existing matching raw upload
     * and returns any existing property ID or false
     *
     * @param $address string
     * @return int bool
     */
    private function checkForRawID($address)
    {
        $raw = RawAddress::where('address', $address)->first();

        if($raw)
        {
            return $raw->property_id;
        }
        else
        {
            return false;
        }
    }


    /**
     *
     * Identify the house number and check for
     * dashed addresses to return first number
     *
     *
     * @param $address
     * @return bool
     */
    private function setHouseNumber($houseNumber)
    {

        $property['house_number']= $houseNumber;

        //Test for hypenated addresses
        if(strpos($houseNumber, '-'))
        {
            $exploded = explode('-', $property['house_number']);
            $property['house_number'] = $exploded[0];
        }


        // Clear leading zeros
        $property['house_number'] = ltrim($property['house_number'], '0');

        //Test if address is not a zero address
        if (!ctype_digit($houseNumber)) {
            $property = $this->checkForUnitInAddress($property['house_number']);
            if (!ctype_digit($property['house_number'])) {
                $property['house_number'] = null;
            }
        }

        return $property;
    }

    /**
     *
     *
     * @param $address
     * @return mixed
     */
    private function processStreetName($address)
    {
        // check if first object is unit number
        $unit = null;
        if(isset($address[0]) && substr($address[0],0,1) == '#')
        {
            $unit = $address[0];
            unset($address[0]);
            $address = array_values($address);
        }

        $street = null;
        $street_type = null;

        // Check for street names started with st for saint
        if(isset($address[0]) && trim($address[0]) == 'st')
        {
            $street = 'saint';
            unset($address[0]);
            $address = array_values($address);
        }

        // Add each non-street type like
        $streets = config('citynexus.street_types');
        $units = config('citynexus.unit_types');
        $test = true;

        foreach($address as $k => $i)
        {
            if(!isset($streets[$i]) && !isset($units[$i]) && $test )
            {
                if(substr($i,0,1) == '#')
                {
                    $unit = $i;
                    $test = false;
                }
                else{
                    $street .= ' ' . $i;
                }
                unset($address[$k]);
            }
            else
            {
                $test = false;
                if(isset($streets[$i]))
                {
                    $street_type = $streets[$i];
                    unset($address[$k]);
                }
            }
        }

        if($unit == null) {
            if (count($address) > 0) {
                foreach ($address as $i) {
                    $unit .= ' ' . $i;
                }
                $unit = trim($unit);
            }
        }

        //Build return array
        $return = null;
        if($street != null) $return['street_name'] = trim($street);
        if($street_type != null) $return['street_type'] = trim($street_type);
        if($unit != null) $return['unit'] = trim($unit);

        return $return;

    }

    private function checkForUnitInAddress($number)
    {
        if(strpos($number, '#'))
        {
         $exploded = explode('#', $number);
            $return['house_number'] = trim($exploded[0]);
            $return['unit'] = '#' . trim($exploded[1]);
        }
        else
        {
            $return['house_number'] = $number;
        }

        return $return;
    }

}