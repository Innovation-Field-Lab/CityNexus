<?php namespace CityNexus\CityNexus;

use Illuminate\Database\Eloquent\Model;

class Score extends Model {

	protected $fillable = ['elements', 'name', 'period'];

	protected $table = 'citynexus_scores';

}
