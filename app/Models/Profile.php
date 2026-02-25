<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'phone',
        'address',
        'image',
        'type',
    ];

      // ✅ Add this
    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }
    /**
     * Get the user that owns the profile.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get the profile's type.
     */
    public function getTypeAttribute($value)
    {
        return $value;
    }
    
    /**
     * Set the profile's type.
     */
    public function setTypeAttribute($value)
    {
        $this->attributes['type'] = $value;
    }
}