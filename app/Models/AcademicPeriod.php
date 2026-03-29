<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicPeriod extends Model
{
    use HasFactory;

    protected $fillable = [
        'semester',
        'tahun_akademik',
        'start_date',
        'end_date',
        'is_active',
        'description'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean'
    ];

    // Get active period
    public static function getActive()
    {
        return self::where('is_active', true)->first();
    }

    // Get current period display
    public static function getCurrentPeriodDisplay()
    {
        $active = self::getActive();
        if ($active) {
            return $active->semester . ' ' . $active->tahun_akademik;
        }
        return 'Semester Belum Diatur';
    }

    // Get current period for form display
    public static function getCurrentPeriod()
    {
        return self::getActive();
    }
}