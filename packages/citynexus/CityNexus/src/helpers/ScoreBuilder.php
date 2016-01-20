<?php


namespace CityNexus\CityNexus;


use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Salaback\Tabler\Table;

class ScoreBuilder
{
    /**
     * @param $value
     * @param $score
     */

    public function genScore($record, $elements)
    {
        $score = null;

        $today = Carbon::today();

        foreach($elements as $i)
        {
            if($i->scope == 'last')
            {
                if($i->period != null)
                {
                    $value = DB::table($i->table_name)
                        ->where(config('tabler.index_id'), $record->id)
                        ->where('updated_at', '>', $today->subDays($i->period))
                        ->value($i->key);
                }
                else
                {
                    $value = DB::table($i->table_name)->value($i->key);
                }

                $score = $score + $this->calcElement($value, $i);

            }
        }

        return $score;

    }

    public function calcElement($value, $score)
    {
        $return = null;

        if($score->function == 'func') $return = $this->runFunc($value, $score);
        elseif($score->function == 'range') $return = $this->runRange($value, $score);
        return $return;
    }
    private function runFunc($value, $score)
    {
        if($score->func == '+') return $value + $score->value;
        elseif($score->func == '-') return $value - $score->value;
        elseif($score->func == '*') return $value * $score->value;
        elseif($score->func == '/') return $value / $score->value;
        else return null;

    }

    private function runRange($value, $score)
    {
        if($score->range == '>' && $value > $score->test) return $score->result;
        elseif($score->range == '<' && $value < $score->test) return $score->result;

        else return null;

    }
}