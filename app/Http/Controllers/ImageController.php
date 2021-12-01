<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use Kreait\Firebase\Storage;
use Psr\Http\Message\StreamInterface;

class ImageController extends Controller
{
    public function __construct(Storage $storage) {
        $this->bucket = $storage->getBucket();
    }

    public function cropImage($image): StreamInterface
    {
        list($width, $height) = getimagesize($image);
        if ($width != $height) {
            $size = $width < $height ? $width : $height;
            return Image::make($image)->fit($size)->stream('jpg');
        } else {
            return Image::make($image)->stream('jpg');
        }
    }

    public function uploadImage(StreamInterface $streamedImage, string $imagePath): ?string
    {
        $imageLink = env('FIREBASE_STORAGE_URL') . str_replace('/', '%2F', $imagePath) . '?alt=media';
        $upload = $this->bucket->upload($streamedImage->__toString(), [
            'name' => $imagePath
        ]);
        if ($upload) {
            return $imageLink;
        } else {
            return null;
        }
    }

    public function deleteImage(string $imagePath): bool
    {
        $imageObject = $this->bucket->object($imagePath);
        if ($imageObject->exists()) {
            $imageObject->delete();
            return true;
        }
        return false;
    }
}
