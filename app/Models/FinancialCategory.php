<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinancialCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'type', 'area'];

    public function transactions()
    {
        return $this->hasMany(FinancialTransaction::class, 'category_id');
    }
}
