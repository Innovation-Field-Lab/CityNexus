<?php

namespace CityNexus\CityNexus;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $table = 'citynexus_tasks';
    protected $fillable = ['task', 'description', 'assigned_to', 'due_by'];
    protected $dates = ['created_at', 'updated_at', 'due_by', 'completed_at'];

    public function creator()
    {
        return $this->belongsTo('\App\User', 'created_by');
    }

    public function assignee()
    {
        return $this->belongsTo('\App\User', 'assigned_to');
    }

    public function property()
    {
        return $this->morphedByMany('\CityNexus\CityNexus\Property', 'citynexus_taskable');
    }

    public function scopeOpen($query)
    {
        return $query->orderBy('created_at', 'DESC')->orderBy('due_at', 'DESC')->whereNull('completed_at');
    }

    public function scopeClosed($query)
    {
        return $query->orderBy('created_at', 'DESC')->orderBy('due_at', 'DESC')->whereNotNull('completed_at');
    }

    public function scopePastDue($query)
    {
        return $query->orderBy('created_at')->orderBy('due_at')->where('due_at' < Carbon::now());
    }
}
