<?php


namespace CityNexus\CityNexus;

class Helper
{
    static function setting($key)
    {
        return Setting::find($key)->value;
    }
}