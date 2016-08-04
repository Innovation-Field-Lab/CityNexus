<?php namespace CityNexus\CityNexus;

use Illuminate\Database\Eloquent\Model;

class Report extends Model {

	protected $fillable = ['name', 'type', 'settings_json'];

	protected $table = 'citynexus_reports';

	public function getSettingsAttribute()
	{
		return \GuzzleHttp\json_decode($this->settings_json);
	}
}
