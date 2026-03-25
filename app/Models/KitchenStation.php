<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KitchenStation extends Model
{
    protected $fillable = [
        'business_id',
        'location_id',
        'name',
        'description',
        'color',
        'icon',
        'is_active',
        'sort_order',
        'kitchen_printer_id',
        'auto_print_orders'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'auto_print_orders' => 'boolean'
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function location()
    {
        return $this->belongsTo(BusinessLocation::class, 'location_id');
    }

    public function assignments()
    {
        return $this->hasMany(KitchenStationAssignment::class);
    }

    public function kitchenPrinter()
    {
        return $this->belongsTo(KitchenPrinter::class);
    }

    public function productAssignments()
    {
        return $this->assignments()->where('assignable_type', 'product');
    }

    public function categoryAssignments()
    {
        return $this->assignments()->where('assignable_type', 'category');
    }

    public function assignedProducts()
    {
        return $this->belongsToMany(Product::class, 'kitchen_station_assignments', 'kitchen_station_id', 'assignable_id')
                    ->where('kitchen_station_assignments.assignable_type', 'product');
    }

    public function assignedCategories()
    {
        return $this->belongsToMany(Category::class, 'kitchen_station_assignments', 'kitchen_station_id', 'assignable_id')
                    ->where('kitchen_station_assignments.assignable_type', 'category');
    }

    public function getPendingItemsCount()
    {
        $business_id = $this->business_id;
        
        $productIds = $this->assignedProducts()->pluck('products.id');
        
        $categoryIds = $this->assignedCategories()->pluck('categories.id');
        
        $query = \App\Models\TransactionSellLine::leftJoin('transactions as t', 't.id', '=', 'transaction_sell_lines.transaction_id')
            ->leftJoin('variations as v', 'transaction_sell_lines.variation_id', '=', 'v.id')
            ->leftJoin('products as p', 'v.product_id', '=', 'p.id')
            ->where('t.business_id', $business_id)
            ->where('t.type', 'sell')
            ->where('t.status', 'final')
            ->where('t.is_running_order', 1)
            ->where('t.location_id', $this->location_id) 
            ->where(function($q) {
                $q->whereNull('transaction_sell_lines.res_line_order_status')
                  ->orWhere('transaction_sell_lines.res_line_order_status', 'received')
                  ->orWhere('transaction_sell_lines.res_line_order_status', 'preparing');
            });

        // Filter by assigned products and categories
        if ($productIds->count() > 0 || $categoryIds->count() > 0) {
            $query->where(function($q) use ($productIds, $categoryIds) {
                if ($productIds->count() > 0) {
                    $q->orWhere(function($productQ) use ($productIds) {
                        $productQ->whereIn('p.id', $productIds)
                                ->whereExists(function($locationQ) {
                                    $locationQ->select(\DB::raw(1))
                                             ->from('product_locations')
                                             ->whereRaw('product_locations.product_id = p.id')
                                             ->where('product_locations.location_id', $this->location_id);
                                });
                    });
                }
                if ($categoryIds->count() > 0) {
                    $q->orWhere(function($subQ) use ($categoryIds) {
                        $subQ->where(function($categoryQ) use ($categoryIds) {
                            $categoryQ->whereIn('p.category_id', $categoryIds)
                                     ->orWhereIn('p.sub_category_id', $categoryIds);
                        })
                        ->whereExists(function($locationQ) {
                            $locationQ->select(\DB::raw(1))
                                     ->from('product_locations')
                                     ->whereRaw('product_locations.product_id = p.id')
                                     ->where('product_locations.location_id', $this->location_id);
                        });
                    });
                }
            });
        } else {
            // No assignments, return 0
            return 0;
        }

        return $query->count();
    }

    public static function getActiveStations($business_id, $location_id = null)
    {
        $query = self::where('business_id', $business_id)
                     ->where('is_active', true);
        
        if ($location_id) {
            $query->where('location_id', $location_id);
        }
        
        return $query->orderBy('sort_order')
                     ->orderBy('name')
                     ->get();
    }
}