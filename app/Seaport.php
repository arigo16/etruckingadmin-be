<?php 
namespace App;

use Illuminate\Database\Eloquent\Model;

class Seaport extends Model {

    protected $fillable = [        
        'seaport_name',
        'lat_seaport',
        'long_seaport',
        'created_at',
        'updated_at',
        'deleted_at',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    protected $hidden = [ 

    ];
}