<?php

namespace CityNexus\CityNexus;

use App\Jobs\Job;
use App\Property;
use Carbon\Carbon;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;

class GenerateScore extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    private $elements;
    private $table;
    private $properties;

    /**
     * Create a new job instance.
     *
     * @param string $elements
     * @param string $table
     * @param string $property
     */
    public function __construct($elements, $table, $properties)
    {

        $this->elements = $elements;
        $this->table = $table;
        $this->properties = $properties;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if($this->properties == false)
        {
            $score = Score::find($this->table);
            $score->status = 'complete';
            $score->save();

            return null;
        }

        else {
            foreach ($this->properties as $property) {
                //Generate score
                $scoreBuilder = new ScoreBuilder();
                $p_score = $scoreBuilder->genScore($property, $this->elements);

                //Create Score record
                $records[] = [
                    'property_id' => $property->id,
                    'score' => $p_score,
                    'updated_at' => Carbon::now(),
                    'created_at' => Carbon::now()
                ];
            }

            DB::table($this->table)->insert($records);
        }

    }
}
