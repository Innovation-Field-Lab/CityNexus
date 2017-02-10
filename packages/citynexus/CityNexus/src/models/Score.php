<?php namespace CityNexus\CityNexus;

use Illuminate\Database\Eloquent\Model;

class Score extends Model {

	protected $fillable = ['elements', 'name', 'period', 'status', 'scope'];

	protected $table = 'citynexus_scores';

}
