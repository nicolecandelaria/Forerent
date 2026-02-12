<?php

namespace App\Models;

use App\Models\Message;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'user_id';

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'contact',
        'password',
        'profile_img',
        'role'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // If user is a landlord
    public function properties()
    {
        return $this->hasMany(Property::class, 'owner_id', 'user_id');
    }

    // If user is a manager
    public function managedUnits()
    {
        return $this->hasMany(Unit::class, 'manager_id', 'user_id');
    }

    public function receipts()
    {
        return $this->hasMany(Receipt::class, 'manager_id', 'user_id');
    }

    // If user is a tenant
    public function leases()
    {
        return $this->hasMany(Lease::class, 'tenant_id', 'user_id');
    }

    // Announcements authored by user
    public function announcements()
    {
        return $this->hasMany(Announcement::class, 'author_id', 'user_id');
    }

    public function unitsManaged()
    {
        return $this->hasMany(Unit::class, 'manager_id');
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id', 'user_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id', 'user_id');
    }
}
