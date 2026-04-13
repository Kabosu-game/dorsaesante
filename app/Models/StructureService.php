<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StructureService extends Model
{
    protected $fillable = ['health_structure_id', 'name', 'description', 'is_available'];

    protected function casts(): array
    {
        return ['is_available' => 'boolean'];
    }

    public function healthStructure() { return $this->belongsTo(HealthStructure::class); }
}
