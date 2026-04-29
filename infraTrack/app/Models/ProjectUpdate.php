<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectUpdate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'report_id',
        'admin_id',
        'title',
        'description'
    ];

    public function report()
    {
        return $this->belongsTo(Report::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function images()
    {
        return $this->hasMany(ProjectUpdateImage::class);
    }
}