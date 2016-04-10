<?php


namespace CityNexus\CityNexus;


use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ScoreBuilder
{
    /**
     * @param $value
     * @param $score
     */

    public function genScore($record, $elements)
    {
        $score = 0;

        $today = Carbon::today();

        foreach($elements as $i)
        {
            $value = null;

            if($i->scope == 'score')
            {
                if(DB::table('citynexus_scores_' . $i->table_id)->exists())
                {
                    $value = DB::table('citynexus_scores_' . $i->table_id)
                        ->where(config('tabler.index_id'), $record->id)
                        ->pluck('score');
                    if($value != null)
                    {
                        $score = $score + $this->calcElement($value, $i);
                    }
                }

            }

            elseif($i->scope == 'last') {
                if ($i->period != null) {
                    $value = DB::table($i->table_name)
                        ->where(config('citynexus.index_id'), $record->id)
                        ->where('updated_at', '>', $today->subDays($i->period))
                        ->value($i->key);
                } else {
                    $value = DB::table($i->table_name)
                        ->where(config('citynexus.index_id'), $record->id)
                        ->value($i->key);
                }

                if($value != null)
                {
                    $score = $score + $this->calcElement($value, $i);
                }

            }

            elseif($i->scope == 'all')
            {
                if($i->period != null)
                {
                    $values[] = DB::table($i->table_name)
                        ->join('citynexus_properties', $i->table_name . '.' . config('citynexus.index_id'), '=', 'citynexus_properties.id')
                        ->where('updated_at', '>', $today->subDays($i->period))
                        ->where(config('citynexus.index_id'), $record->id)
                        ->orWhere('citynexus_properties.alias_of', $record->id)
                        ->lists($i->key);
                }
                else
                {
                    $values[] = DB::table($i->table_name)
                        ->join('citynexus_properties', $i->table_name . '.' . config('citynexus.index_id'), '=', 'citynexus_properties.id')
                        ->where(config('citynexus.index_id'), $record->id)
                        ->orWhere('citynexus_properties.alias_of', $record->id)
                        ->lists($i->key);
                }
                if($values != null)
                {
                    foreach($values as $value) {
                        foreach($value as $iv)
                        {
                            $score = $score + $this->calcElement($iv, $i);
                        }
                    }
                }
            }
        }

        return $score;
    }

    public function calcElement($value, $score)
    {

        $return = null;

        if($score->function == 'func') $return = $this->runFunc($value, $score);
        elseif($score->function == 'float') $return = $this->runFunc($value, $score);
        elseif($score->function == 'range') $return = $this->runRange($value, $score);
        elseif($score->function == 'empty') $return = $this->runRange($value, $score);
        else $return = $this->runText($value, $score);

        return $return;
    }

    private function runFunc($value, $score)
    {
        if($score->func == '+') $return = $value + $score->factor;
        elseif($score->func == '-') $return = $value - $score->factor;
        elseif($score->func == '*') $return = $value * $score->factor;
        elseif($score->func == '/') $return = $value / $score->factor;
        else $return = null;

        return $return;

    }

    private function runRange($value, $score)
    {
        if($score->range == '>' && $value > $score->test) return $score->result;
        elseif($score->range == '<' && $value < $score->test) return $score->result;
        elseif($score->range == '=' && $value == $score->test) return $score->result;

        else return null;

    }

    private function runText($value, $score)
    {
        if($score->function == 'notempty' && $value != null) { return $score->result; }
        elseif($score->function == 'empty' && $value == null) { return $score->result; }
        elseif($score->function == 'equals' && $value == $score->test) { return $score->result; }
        elseif($score->function == 'doesntequal' && $value != $score->test) { return $score->result; }
        elseif($score->function == 'contains'&& strpos($value, $score->test) != false) { return $score->result; }
        elseif($score->function == 'doesntcontain'&& strpos($value, $score->test) == false) { return $score->result; }
        else return null;
    }

}
