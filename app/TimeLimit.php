<?php 
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TimeLimit extends Model {
    use SoftDeletes;

    protected $table = "setting_apps";

    protected $fillable = [
        'setting_type',
        'setting_value',
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