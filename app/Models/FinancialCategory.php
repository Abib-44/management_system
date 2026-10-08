<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinancialCategory extends Model
{
    use HasFactory;

    /**
     * Valore dell'ENUM `area` usato per le categorie della scuola.
     * Se un giorno vuoi cambiarlo, modifichi solo questa riga.
     */
    public const SCHOOL_AREA = 'management';

    protected $fillable = ['name', 'type', 'area'];

    public function transactions()
    {
        return $this->hasMany(FinancialTransaction::class, 'category_id');
    }

    /**
     * Un utente "solo scuola" vede solo le categorie della scuola.
     * Gli altri ruoli vedono tutte le categorie.
     */
    public function scopeVisibleTo(Builder $query, ?User $user): Builder
    {
        return $user?->isSchoolOnly()
            ? $query->where('area', self::SCHOOL_AREA)
            : $query;
    }
}