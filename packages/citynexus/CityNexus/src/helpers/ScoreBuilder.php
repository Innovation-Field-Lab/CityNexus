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
                        ->join('citynexus_properties', $i->table_name . '.' . config('citynexus.index_id'), '=', 'citynexus_properties.id')
                        ->where(config('citynexus.index_id'), $record->id)
                        ->orWhere('citynexus_properties.alias_of', $record->id)
                        ->where('updated_at', '>', $today->subDays($i->period))
                        ->value($i->key);
                } else {
                    $value = DB::table($i->table_name)
                        ->join('citynexus_properties', $i->table_name . '.' . config('citynexus.index_id'), '=', 'citynexus_properties.id')
                        ->where(config('citynexus.index_id'), $record->id)
                        ->orWhere('citynexus_properties.alias_of', $record->id)
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

        switch ($score->function)
        {
            case 'func':
                $return = $this->runFunc($value, $score);
                break;
            case 'float':
                $return = $this->runFunc($value, $score);
                break;
            case 'range':
                $return = $this->runRange($value, $score);
                break;
            default:
                $return = $this->runText($value, $score);
        }

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
        elseif($score->function == 'contains'&& stripos($value, $score->test) != false) { return $score->result; }
        elseif($score->function == 'doesntcontain'&& stripos($value, $score->test) === false) { return $score->result; }
        else return null;
    }

}
