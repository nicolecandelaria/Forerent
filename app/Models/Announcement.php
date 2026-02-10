<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Announcement extends Model
{
    use SoftDeletes, HasFactory;

    protected $primaryKey = 'announcement_id';

    protected $fillable = [
        'author_id', 'property_id', 'headline', 'details', 'sender_role', 'recipient_role', 'created_at'
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id', 'user_id');
    }

    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id', 'property_id');
    }
}
