<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KitchenPrinter extends Model
{
    protected $fillable = [
        'business_id',
        'location_id',
        'name',
        'description',
        'printer_type',
        'print_method',
        'print_server_url',
        'use_print_server',
        'connection_string',
        'port',
        'printer_profile',
        'paper_width',
        'auto_cut_paper',
        'encoding',
        'settings',
        'is_active',
        'auto_print'
    ];

    protected $casts = [
        'settings' => 'array',
        'is_active' => 'boolean',
        'auto_print' => 'boolean',
        'use_print_server' => 'boolean',
        'auto_cut_paper' => 'boolean',
        'port' => 'integer',
        'paper_width' => 'integer'
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function location()
    {
        return $this->belongsTo(BusinessLocation::class, 'location_id');
    }

    public function kitchenStations()
    {
        return $this->hasMany(KitchenStation::class);
    }

    public function printJobs()
    {
        return $this->hasMany(PrintJob::class);
    }

    public function getConnectionDisplayAttribute()
    {
        switch ($this->printer_type) {
            case 'network':
                return $this->connection_string . ($this->port ? ':' . $this->port : '');
            case 'usb':
                return 'USB: ' . $this->connection_string;
            case 'bluetooth':
                return 'Bluetooth: ' . $this->connection_string;
            default:
                return $this->connection_string;
        }
    }

    public function getStatusAttribute()
    {
        return $this->is_active ? 'online' : 'offline';
    }

    public static function getActiveForBusiness($business_id, $location_id = null)
    {
        $query = self::where('business_id', $business_id)
                     ->where('is_active', true);
        
        if ($location_id) {
            $query->where('location_id', $location_id);
        }
        
        return $query->orderBy('name')->get();
    }

    public function testConnection()
    {
        try {
            if ($this->printer_type === 'network') {
                $connection = @fsockopen($this->connection_string, $this->port ?: 9100, $errno, $errstr, 5);
                if ($connection) {
                    fclose($connection);
                    return ['success' => true, 'message' => 'Printer is reachable'];
                } else {
                    return ['success' => false, 'message' => "Connection failed: $errstr ($errno)"];
                }
            } else {
                // For USB and Bluetooth, we'd need platform-specific checks
                return ['success' => true, 'message' => 'Connection test not implemented for this printer type'];
            }
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}