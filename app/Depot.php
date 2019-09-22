<?php 
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Depot extends Model {

    use SoftDeletes;

    protected $fillable = [
        'depot_name',
        'depot_address',
        'depot_email',
        'depot_phone',
        'depot_price',
        'created_at',
        'updated_at',
        'deleted_at',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $hidden = [ 

    ];

    protected $dates = ['deleted_at'];
}