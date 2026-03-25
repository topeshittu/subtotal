<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KitchenStationAssignment extends Model
{
    protected $fillable = [
        'kitchen_station_id',
        'assignable_type',
        'assignable_id'
    ];

    public function kitchenStation()
    {
        return $this->belongsTo(KitchenStation::class);
    }

    public function assignable()
    {
        return $this->morphTo();
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'assignable_id')->where('assignable_type', 'product');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'assignable_id')->where('assignable_type', 'category');
    }
}