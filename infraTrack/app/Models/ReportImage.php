<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReportImage extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'report_id',
        'image_path',
        'created_at'
    ];

    public function report()
    {
        return $this->belongsTo(Report::class);
    }
}