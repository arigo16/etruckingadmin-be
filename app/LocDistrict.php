<?php 
namespace App;

use Illuminate\Database\Eloquent\Model;

class LocDistrict extends Model {

    protected $fillable = [        
        'regency_id',
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
        return $this->belongsTo('App\LocRegency');
    }
}