<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Report extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'description',
        'severity',
        'is_anonymous',
        'status',
        'location_name',
        'district',
        'latitude',
        'longitude',
        'coordinates',
        'priority_score'
    ];

    protected function casts(): array
    {
        return [
            'is_anonymous' => 'boolean',
            'priority_score' => 'float',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images()
    {
        return $this->hasMany(ReportImage::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function projectUpdates()
    {
        return $this->hasMany(ProjectUpdate::class);
    }
}