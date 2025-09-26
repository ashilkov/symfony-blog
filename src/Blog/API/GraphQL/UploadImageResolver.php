<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\API\GraphQL;

use App\Blog\API\DTO\Response\UploadResult;
use GraphQL\Type\Definition\ResolveInfo;
use Symfony\Component\HttpFoundation\File\UploadedFile;

readonly class UploadImageResolver
{
    public function __construct(
        private string $uploadDir = __DIR__.'/../../../../public/uploads',
    ) {
    }

    /**
     * @param array<string, mixed>      $args    expects ['file' => UploadedFile]
     * @param array<string, mixed>|null $context
     */
    public function __invoke(mixed $root, array $args, ?array $context = null, ?ResolveInfo $info = null): UploadResult
    {
        if (!isset($args['args']['input']['file']) || !$args['args']['input']['file'] instanceof UploadedFile) {
            throw new \InvalidArgumentException('Missing or invalid "file" argument.');
        }

        $file = $args['args']['input']['file'];

        $allowedMimes = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/gif' => 'gif',
        ];

        $detectedMime = $file->getMimeType();
        if (null === $detectedMime && is_readable($file->getPathname())) {
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $detectedMime = $finfo->file($file->getPathname()) ?: null;
        }

        if (null === $detectedMime || !array_key_exists($detectedMime, $allowedMimes)) {
            throw new \InvalidArgumentException('Only image uploads are allowed (jpeg, png, webp, gif).');
        }

        if (!is_dir($this->uploadDir) && !@mkdir($concurrentDirectory = $this->uploadDir, 0775, true) && !is_dir($concurrentDirectory)) {
            throw new \RuntimeException(sprintf('Failed to create directory: %s', $this->uploadDir));
        }

        $originalName = pathinfo($file->getClientOriginalName() ?? 'upload', PATHINFO_FILENAME);
        $safeBase = preg_replace('/[^A-Za-z0-9_\-]/', '_', (string) $originalName) ?: 'upload';
        $extension = $allowedMimes[$detectedMime] ?? 'bin';
        $filename = sprintf('%s_%s.%s', $safeBase, bin2hex(random_bytes(6)), $extension);

        $file->move($this->uploadDir, $filename);

        $publicUrl = '/uploads/'.$filename;

        // Use a synthetic identifier (e.g., the filename or the public URL)
        return new UploadResult(id: $filename, url: $publicUrl);
    }
}
