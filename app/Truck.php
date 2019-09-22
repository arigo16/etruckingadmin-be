<?php 
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Truck extends Model {

    use SoftDeletes;

    protected $fillable = [        
        'vendor_id',
        'truck_type_id',
        'box_type_id',
        'truck_feet_id',
        'plat_number',
        'merk',
        'model',
        'status',
        'image_stnk',
        'image_interior',
        'image_front',
        'image_back',
        'image_kir_head',
        'kir_head_number',
        'kir_head_expiry',
        'image_kir_trailer',
        'kir_trailer_number',
        'kir_trailer_expiry',
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

    public function truck_type()
    {
        return $this->belongsTo('App\TruckType', 'truck_type_id');
    }

    public function box_type()
    {
        return $this->belongsTo('App\BoxType', 'box_type_id');
    }

    public function truck_feet()
    {
        return $this->belongsTo('App\TruckFeet', 'truck_feet_id');
    }

    public function truck_stock()
    {
        return $this->hasOne('App\TruckStock');
    }

    public function vendor()
    {
        return $this->belongsTo('App\Vendor');
    }
}