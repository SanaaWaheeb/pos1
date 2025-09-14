<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shipping extends Model
{
    protected $fillable = [
        'name',
        'price',
        'location_id',
        'store_id',
        'created_by',
    ];
    private static $location = null;
    public function locationName()
    {
        if(!isset($this->location_name)){
            $result =  Location::whereIn('id',explode(',',$this->location_id))->get()->pluck('name')->toArray();
            $this->location_name = implode(', ',$result);

        }
        return $this->location_name;
    }
}
