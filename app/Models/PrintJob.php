<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrintJob extends Model
{
    protected $fillable = [
        'business_id',
        'kitchen_printer_id',
        'transaction_id',
        'job_type',
        'print_content',
        'status',
        'error_message',
        'printed_at',
        'retry_count'
    ];

    protected $casts = [
        'printed_at' => 'datetime',
        'retry_count' => 'integer'
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function kitchenPrinter()
    {
        return $this->belongsTo(KitchenPrinter::class);
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function markAsPrinting()
    {
        $this->update([
            'status' => 'printing'
        ]);
    }

    public function markAsCompleted()
    {
        $this->update([
            'status' => 'completed',
            'printed_at' => now()
        ]);
    }

    public function markAsFailed($errorMessage)
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $errorMessage,
            'retry_count' => $this->retry_count + 1
        ]);
    }

    public function canRetry()
    {
        return $this->retry_count < 3 && $this->status === 'failed';
    }

    public static function createOrderPrintJob($business_id, $printer_id, $transaction_id, $content)
    {
        return self::create([
            'business_id' => $business_id,
            'kitchen_printer_id' => $printer_id,
            'transaction_id' => $transaction_id,
            'job_type' => 'order',
            'print_content' => $content,
            'status' => 'pending'
        ]);
    }

    public static function getPendingJobs($business_id, $printer_id = null)
    {
        $query = self::where('business_id', $business_id)
                     ->where('status', 'pending')
                     ->orderBy('created_at', 'asc');

        if ($printer_id) {
            $query->where('kitchen_printer_id', $printer_id);
        }

        return $query->get();
    }
}