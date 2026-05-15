<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

trait HasCustomId
{
    protected static function bootHasCustomId()
    {
        static::creating(function ($model) {
            if (!$model->getKey()) {
                $model->{$model->getKeyName()} = $model->generateCustomId();
            }
        });
    }

    public function generateCustomId()
    {
        $prefix = $this->getPrefix();
        $table = $this->getTable();
        $pk = $this->getKeyName();

        // Special handling for Subkriteria (C1.1, C1.2, etc.)
        if ($table === 'subkriteria') {
            $kriteriaId = $this->kriteria_id; // Assuming it's already set
            $lastId = DB::table($table)
                ->where('kriteria_id', $kriteriaId)
                ->where($pk, 'LIKE', $kriteriaId . '.%')
                ->orderByRaw("LENGTH($pk) DESC, $pk DESC")
                ->value($pk);

            if (!$lastId) {
                return $kriteriaId . '.1';
            }

            $lastNum = (int) Str::afterLast($lastId, '.');
            return $kriteriaId . '.' . ($lastNum + 1);
        }

        // Special handling for Kriteria (C1, C2, etc.)
        if ($table === 'kriteria') {
            $lastId = DB::table($table)
                ->where($pk, 'REGEXP', '^C[0-9]+$')
                ->orderByRaw('CAST(SUBSTRING(' . $pk . ', 2) AS UNSIGNED) DESC')
                ->value($pk);

            if (!$lastId) {
                return 'C1';
            }

            $lastNum = (int) substr($lastId, 1);
            return 'C' . ($lastNum + 1);
        }

        // Standard handling (U001, S001, etc.)
        $lastId = DB::table($table)
            ->where($pk, 'LIKE', $prefix . '%')
            ->orderBy($pk, 'desc')
            ->value($pk);

        if (!$lastId) {
            return $prefix . '001';
        }

        $lastNum = (int) Str::after($lastId, $prefix);
        return $prefix . str_pad($lastNum + 1, 3, '0', STR_PAD_LEFT);
    }

    abstract public function getPrefix();
}
