<?php

namespace CityNexus\CityNexus;

use App\Jobs\Job;
use Carbon\Carbon;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;

class GenerateScore extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    private $element;
    private $score_id;


    /**
     * Create a new job instance.
     *
     * @param $element
     * @param $score_id
     * @internal param string $table
     * @internal param string $property
     */
    public function __construct($element, $score_id)
    {
        $this->element = $element;

        $this->score_id = $score_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $key = $this->element->key;
        $scorebuilder = new ScoreBuilder();
        $values = DB::table($this->element->table_name)->whereNotNull($key)->select('property_id', $key)->get();
        $oldscores = DB::table('citynexus_scores_' . $this->score_id)->select('property_id', 'score', 'id')->get();

        $scores = array();

        foreach($oldscores as $i)
        {
            $scores[$i->property_id] = [
                'property_id' => $i->property_id,
                'score' => $i->score
            ];
        }

        foreach($values as $value)
        {
            $new_score = $scores[$value->property_id]['score'] + $scorebuilder->calcElement($value->$key, $this->element);
            $scores[$value->property_id] = [
                'property_id' => $value->property_id,
                'score' => $new_score,
            ];

        }

        DB::table('citynexus_scores_' . $this->score_id)->truncate();
        DB::table('citynexus_scores_' . $this->score_id)->insert($scores);

    }
}
