<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaUploadService
{
    protected string $tenantId;

    public function __construct()
    {
        $this->tenantId = tenant('id') ?? 'central';
    }

    /**
     * Upload file directly to DigitalOcean Spaces.
     */
    public function upload($file, string $type, ?string $entityId = null): array
    {
        $path = $this->buildPath($type, $entityId);

        $fileName = $this->generateFileName($file);

        $fileKey = "{$path}/{$fileName}";

        // Upload to DO Spaces
        Storage::disk('spaces')->put($fileKey, file_get_contents($file), 'public');

        return [
            'file_key' => $fileKey,
            'cdn_url'  => $this->buildCdnUrl($fileKey),
            'mime'     => $file->getClientMimeType(),
            'size'     => $file->getSize(),
        ];
    }

    /**
     * Generate a presigned upload URL for Vue frontend.
     */
    public function createPresignedUploadUrl(
        string $fileName,
        string $type,
        ?string $entityId = null,
        string $contentType = 'image/jpeg'
    ): array
    {
        $fileKey = $this->generateKey($type, $fileName, $entityId);

        // Get Spaces client (DigitalOcean uses S3-compatible SDK)
        $adapter = Storage::disk('spaces')->getAdapter();
        $client  = $adapter->getClient();

        $bucket = config('filesystems.disks.spaces.bucket');

        $command = $client->getCommand('PutObject', [
            'Bucket'      => $bucket,
            'Key'         => $fileKey,
            'ContentType' => $contentType,
            'ACL'         => 'public-read',
        ]);

        // Create presigned URL that expires in 10 minutes
        $request = $client->createPresignedRequest($command, '+10 minutes');

        return [
            'upload_url' => (string) $request->getUri(),
            'file_key'   => $fileKey,
            'cdn_url'    => $this->buildCdnUrl($fileKey),
        ];
    }

    /**
     * Build the tenant-aware path.
     */
    protected function buildPath(string $type, ?string $entityId = null): string
    {
        return match ($type) {
            'logo'      => "tenants/{$this->tenantId}/logo",
            'banner'    => "tenants/{$this->tenantId}/banners",
            'product'   => "tenants/{$this->tenantId}/products/{$entityId}",
            'category'  => "tenants/{$this->tenantId}/categories/{$entityId}",
            'user'      => "tenants/{$this->tenantId}/users/{$entityId}",
            'invoice'   => "tenants/{$this->tenantId}/invoices",
            default     => "tenants/{$this->tenantId}/misc",
        };
    }

    /**
     * Generate the S3 key for presigned uploads.
     */
    protected function generateKey(string $type, string $fileName, ?string $entityId = null): string
    {
        $path = $this->buildPath($type, $entityId);

        return "{$path}/" . Str::uuid() . "_" . $fileName;
    }

    /**
     * Generate unique file name for normal uploads.
     */
    protected function generateFileName($file): string
    {
        return Str::uuid() . '.' . $file->getClientOriginalExtension();
    }

    /**
     * Build the CDN URL.
     */
    protected function buildCdnUrl(string $fileKey): string
    {
        $cdnUrl = rtrim(env('DO_SPACES_URL'), '/');
        return "{$cdnUrl}/{$fileKey}";
    }

    public function uploadRaw(
    string $binary,
    string $fileName,
    string $type,
    ?string $entityId = null,
    string $mime = 'application/pdf'
): array {
    $path = $this->buildPath($type, $entityId);
    $fileKey = "{$path}/{$fileName}";

    Storage::disk('spaces')->put($fileKey, $binary, [
        'visibility' => 'public',
        'ContentType' => $mime,
    ]);

    return [
        'file_key' => $fileKey,
        'cdn_url'  => $this->buildCdnUrl($fileKey),
        'mime'     => $mime,
        'size'     => strlen($binary),
    ];
}

}
