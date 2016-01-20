<?php namespace CityNexus\CityNexus;

use Illuminate\Database\Eloquent\Model;

class Property extends Model {

	protected $fillable = ['full_address', 'house_number', 'street', 'unit', 'city', 'state', 'zip', 'lat', 'long'];

	public function score()
	{
		return $this->hasMany('App\Score');
	}

	public function currentScore()
	{
		return $this->score()->orderBy('created_at')->pluck('score');
	}

	public function police()
	{
		return $this->hasMany('App\Police');
	}

	public function fire()
	{
		return $this->hasMany('App\Fire');
	}

	public function fireScore()
	{
		return $this->fire()->orderBy('created_at', 'desc')->pluck('score');
	}

	public function policeScore()
	{
		return $this->police()->orderBy('created_at', 'desc')->pluck('score');
	}

	public function notes()
	{
		return $this->hasMany('App\Note');
	}

	public function assessor()
	{
		return $this->hasOne('App\Assessor');
	}

	public function assessorRecord()
	{
		return $this->assessor()->orderBy('created_at', 'desc')->first();
	}

	public function tax()
	{
		return $this->hasMany('App\Tax');
	}

	public function taxRecord()
	{
		return $this->tax()->orderBy('created_at', 'desc')->first();
	}

	public function finRisk()
	{
		$tax = $this->tax()->orderBy('created_at', 'desc')->first();
		if($tax != null)
			return Risk::finRisk($tax);
		else
			return null;
	}

}
