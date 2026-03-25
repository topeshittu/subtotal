<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionSellLine extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Temporary storage for tracking changes (not saved to database)
     *
     * @var array
     */
    protected $modification_data = [];

    /**
     * Boot method to add model event listeners for order modification tracking
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($model) {
            self::logModification($model, 'created');
        });

        static::updating(function ($model) {
            $changes = [];
            $action = 'updated';

            if ($model->isDirty('quantity') && $model->getOriginal('quantity') != $model->quantity) {
                $changes['quantity'] = [
                    'old' => $model->getOriginal('quantity'),
                    'new' => $model->quantity
                ];
                $action = 'quantity_changed';
            }

            if ($model->isDirty('sell_line_note')) {
                $changes['note'] = [
                    'old' => $model->getOriginal('sell_line_note'),
                    'new' => $model->sell_line_note
                ];
                $action = 'note_changed';
            }

            if ($model->isDirty('combo_modifier_ids') && $model->getOriginal('combo_modifier_ids') != $model->combo_modifier_ids) {
                $changes['combo_modifiers'] = [
                    'old' => $model->getOriginal('combo_modifier_ids'),
                    'new' => $model->combo_modifier_ids
                ];
                $action = 'combo_modifiers_changed';
            }

            $model->modification_data = [
                'changes' => $changes,
                'action' => $action
            ];

    
        });

        static::updated(function ($model) {
           
            if (!empty($model->modification_data['changes'])) {
                self::logModification($model, $model->modification_data['action'], $model->modification_data['changes']);
            } else {
               //
            }
        });

        static::deleting(function ($model) {
            self::logModification($model, 'deleted');
        });
    }
    
    public function transaction()
    {
        return $this->belongsTo(\App\Models\Transaction::class, 'transaction_id');
    }

    public function product()
    {
        return $this->belongsTo(\App\Models\Product::class, 'product_id');
    }

    public function variation()
    {
        return $this->belongsTo(\App\Models\Variation::class, 'variation_id');
    }
    public function variations()
    {
        return $this->belongsTo(\App\Models\Variation::class, 'variation_id');
    }

    public function modifiers()
    {
        return $this->hasMany(\App\Models\TransactionSellLine::class, 'parent_sell_line_id')
            ->where('children_type', 'modifier');
    }

    public function sell_line_purchase_lines()
    {
        return $this->hasMany(\App\Models\TransactionSellLinesPurchaseLines::class, 'sell_line_id');
    }

    /**
     * Get the quantity column.
     *
     * @param  string  $value
     * @return float $value
     */
    public function getQuantityAttribute($value)
    {
        return (float)$value;
    }

    public function lot_details()
    {
        return $this->belongsTo(\App\Models\PurchaseLine::class, 'lot_no_line_id');
    }

    public function get_discount_amount()
    {
        $discount_amount = 0;
        if (!empty($this->line_discount_type) && !empty($this->line_discount_amount)) {
            if ($this->line_discount_type == 'fixed') {
                $discount_amount = $this->line_discount_amount;
            } elseif ($this->line_discount_type == 'percentage') {
                $discount_amount = ($this->unit_price_before_discount * $this->line_discount_amount) / 100;
            }
        }
        return $discount_amount;
    }

    /**
     * Get the unit associated with the purchase line.
     */
    public function sub_unit()
    {
        return $this->belongsTo(\App\Models\Unit::class, 'sub_unit_id');
    }

    public function order_statuses()
    {
        $statuses = [
            'received',
            'cooked',
            'served'
        ];
    }

    public function service_staff()
    {
        return $this->belongsTo(\App\Models\User::class, 'res_service_staff_id');
    }

    /**
     * The warranties that belong to the sell lines.
     */
    public function warranties()
    {
        return $this->belongsToMany('App\Models\Warranty', 'sell_line_warranties', 'sell_line_id', 'warranty_id');
    }

    public function line_tax()
    {
        return $this->belongsTo(\App\Models\TaxRate::class, 'tax_id');
    }

    public function so_line()
    {
        return $this->belongsTo(\App\Models\TransactionSellLine::class, 'so_line_id');
    }
    public function combo_products()
    {
        return $this->hasMany(TransactionSellLine::class, 'parent_sell_line_id')->where('children_type', 'combo');
    }

    /**
     * Log modification to audit table
     */
    private static function logModification($model, $action, $changes = null)
    {
        try {
           if (!class_exists(\Modules\Restaurant\Services\KitchenPrinterService::class)) {
                return;
            }

            $transaction = $model->transaction ?? \App\Models\Transaction::find($model->transaction_id);

            if (!$transaction) {
                return;
            }

            if (!$transaction->is_running_order) {
                return;
            }

            $insert_data = [
                'business_id' => $transaction->business_id,
                'order_id' => $model->transaction_id,
                'order_number' => $transaction->invoice_no,
                'item_id' => $model->id,
                'item_name' => $model->product->name ?? 'Unknown Item',
                'variation_name' => $model->variation->name ?? '',
                'quantity' => $model->quantity,
                'note' => $model->sell_line_note,
                'action' => $action,
                'combo_modifier_action' => ($action === 'combo_modifiers_changed') ? $action : null,
                'modifier_type' => $model->children_type ?? null,
                'parent_item_id' => $model->parent_sell_line_id ?? null,
                'old_values' => $changes ? json_encode($changes) : json_encode($model->getOriginal()),
                'new_values' => json_encode([
                    'quantity' => $model->quantity,
                    'note' => $model->sell_line_note,
                    'status' => $model->res_line_order_status
                ]),
                'created_at' => now(),
            ];

            $unique_id = uniqid();
          

            $insert_result = \DB::table('order_modifications_log')->insert($insert_data);

          
            self::broadcastModification($model, $action, $changes);
            
        } catch (\Exception $e) {
            \Log::error('Failed to log order modification: ' . $e->getMessage());
        }
    }

    /**
     * Broadcast real-time modification to kitchen displays
     */
    private static function broadcastModification($model, $action, $changes = null)
    {
        $transaction = $model->transaction ?? \App\Models\Transaction::find($model->transaction_id);
        
        if (!$transaction) {
            return;
        }

        $notification_data = [
            'type' => 'order_modified',
            'action' => $action,
            'order_id' => $model->transaction_id,
            'order_number' => $transaction->invoice_no,
            'item_id' => $model->id,
            'item_name' => $model->product->name ?? 'Unknown Item',
            'changes' => $changes,
            'timestamp' => now()->timestamp * 1000,
        ];

    }

    /**
     * Get modifications for specific order since timestamp
     */
    public static function getOrderModifications($order_id, $since = null)
    {
        $query = \DB::table('order_modifications_log')
            ->where('order_id', $order_id)
            ->orderBy('created_at', 'desc');

        if ($since) {
            $query->where('created_at', '>', $since);
        }

        return $query->get();
    }

    /**
     * Get all recent modifications for business
     */
    public static function getBusinessModifications($business_id, $since, $limit = 100)
    {
        return \DB::table('order_modifications_log')
            ->where('business_id', $business_id)
            ->where('created_at', '>', $since)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
    
}