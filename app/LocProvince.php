<?php 
namespace App;

use Illuminate\Database\Eloquent\Model;

class LocProvince extends Model {

    protected $fillable = [
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

    public function regency()
    {
        return $this->hasMany('App\LocRegency', 'province_id');
    }
}