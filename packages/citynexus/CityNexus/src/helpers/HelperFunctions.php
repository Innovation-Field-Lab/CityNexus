<?php

function setting( $key )
{
    $setting = \CityNexus\CityNexus\Setting::where('key', $key)->first();
    if($setting == null)
    {
        return null;
    }
    else
    {
        return $setting->value;
    }
}
