<?php

namespace CityNexus\CityNexus;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class File extends Model
{
    use SoftDeletes;
    protected $table = 'citynexus_files';
    protected $fillable = ['caption', 'description', 'version_id', 'location_id', 'property_id'];

    public function property()
    {
        return $this->belongsTo('\CityNexus\CityNexus\Property');
    }

    public function location()
    {
        return $this->belongsTo('\CityNexus\CityNexus\Location');
    }

    public function getFile()
    {
        return $this->current->source;
    }

    public function versions()
    {
        return $this->hasMany('\CityNexus\CityNexus\FileVersion');
    }

    public function current()
    {
        return $this->hasOne('\CityNexus\CityNexus\FileVersion');
    }

    public function updateFile($file)
    {
        $version_id = DB::table("citynexus_file_versions")->insert([
            'added_by'   => Auth::getUser()->id,
            'file_id'   => $this->id,
            'source'    => $file,
            'added_at'  => date("Y-m-d H:i:s")
        ]);

        $this->version_id = $version_id;
        $this->save();
    }
}
