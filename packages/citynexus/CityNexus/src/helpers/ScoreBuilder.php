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

    public function calcScore($value, $score)
    {
        $return = null;

        if($score['type'] == 'func') $return = $this->runFunc($value, $score);
        elseif($score['type'] == 'range') $return = $this->runRange($value, $score);
        return $return;
    }
    private function runFunc($value, $score)
    {
        if($score['func'] == '+') return $value + $score['value'];
        elseif($score['func'] == '-') return $value - $score['value'];
        elseif($score['func'] == '*') return $value * $score['value'];
        elseif($score['func'] == '/') return $value / $score['value'];
        else return null;

    }

    private function runRange($value, $score)
    {
        if($score['func'] == '>' && $value > $score['test']) return $score['result'];
        elseif($score['func'] == '<' && $value < $score['test']) return $score['result'];

        else return null;

    }
}