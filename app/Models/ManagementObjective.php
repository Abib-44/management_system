<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManagementObjective extends Model
{
    use HasFactory;

    protected $fillable = [
        'registration_date', 'meeting', 'objective', 'responsible_id',
        'start_date', 'end_date', 'status', 'priority',
    ];

    protected $casts = [
        'registration_date' => 'date',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function responsible()
    {
        return $this->belongsTo(User::class, 'responsible_id');
    }
}
