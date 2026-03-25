<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
     protected $guarded = [];

    protected static function booted()
    {
        // auto delete from S3 when a media record is deleted
        static::deleting(function ($media) {
            if ($media->file_key) {
                Storage::disk('s3')->delete($media->file_key);
            }
        });
    }

    public function owner()
    {
        // ex: product → hasMany Media
        return $this->morphTo();
    }
}
