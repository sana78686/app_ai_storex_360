<?php

namespace App\Traits;

use App\Models\Tenant\Media;

trait HasMedia
{
    public function media()
    {
        return $this->hasMany(Media::class, 'entity_id')
                    ->where('tenant_id', $this->tenant_id)
                    ->where('type', $this->getMediaType());
    }

    /**
     * Override this in your models
     */
    public function getMediaType()
    {
        return strtolower(class_basename($this)); // ex: Product -> "product"
    }

    /**
     * Get first image
     */
    public function image()
    {
        return $this->media()->first();
    }

    /**
     * Get gallery
     */
    public function gallery()
    {
        return $this->media()->get();
    }
}
