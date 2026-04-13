<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Zone extends Model
{
    protected $fillable = ['name', 'code', 'type', 'parent_id', 'latitude', 'longitude'];

    public function parent() { return $this->belongsTo(Zone::class, 'parent_id'); }
    public function children() { return $this->hasMany(Zone::class, 'parent_id'); }
    public function healthStructures() { return $this->hasMany(HealthStructure::class); }
    public function users() { return $this->hasMany(User::class); }
}
