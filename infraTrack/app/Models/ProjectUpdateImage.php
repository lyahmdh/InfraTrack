<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProjectUpdateImage extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'project_update_id',
        'image_path',
        'created_at'
    ];

    public function projectUpdate()
    {
        return $this->belongsTo(ProjectUpdate::class);
    }
}