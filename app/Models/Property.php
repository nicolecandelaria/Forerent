<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Property extends Model
{
    use SoftDeletes, HasFactory;

    protected $primaryKey = 'property_id';

    protected $fillable = [
        'owner_id',
        'building_name',
        'address',
        'prop_description',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id', 'user_id');
    }

    public function units()
    {
        return $this->hasMany(Unit::class, 'property_id', 'property_id');
    }

    public function announcements()
    {
        return $this->hasMany(Announcement::class, 'property_id', 'property_id');
    }

    public function managers()
    {
        return $this->hasManyThrough(
            User::class,
            Unit::class,
            'property_id',
            'user_id',
            'property_id',
            'manager_id'
        )->distinct();
    }

    public function tenantsForManager($managerId)
    {
        return User::whereHas('leases.bed.unit', function ($query) use ($managerId) {
            $query->where('manager_id', $managerId)
                ->where('property_id', $this->property_id)->distinct();
        });
    }

}
