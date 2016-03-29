<?php


namespace CityNexus\CityNexus;

class Typer
{

    public function type( $object )
    {
        if(gettype($object) == "object") { return 'datetime'; }
        elseif(gettype($object) == "integer") { return 'integer'; }
        elseif(gettype($object) == "boolean") { return 'boolean'; }
        elseif($type = $this->isIntegerOrFloat($object)) { return $type;  }
        elseif($type = $this->isStringOrText($object)) { return $type; }
        return null;
    }

    public function isIntegerOrFloat($object)
    {
        if(gettype($object) == 'double') {
            $integer = intval($object);
            if($integer == $object) return 'integer';
            else return 'float';
        }
        else
            return false;
    }

    public function isStringOrText($object)
    {
        if(gettype($object) == 'string') {
            if (strlen($object) < 150) {
                return 'string';
            }
            else {
                return 'textarea';
            }
        }
        else {
            return false;
        }
    }


}