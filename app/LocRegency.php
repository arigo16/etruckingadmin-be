<?php 
namespace App;

use Illuminate\Database\Eloquent\Model;

class LocRegency extends Model {

    protected $fillable = [        
        'province_id',
        'name',
        'created_at',
        'updated_at',
        'deleted_at',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    protected $hidden = [ 

    ];

    public function province()
    {
        return $this->belongsTo('App\LocProvince', 'province_id');
    }

    public function district()
    {
        return $this->hasMany('App\LocDistrict', 'regency_id');
    }
}