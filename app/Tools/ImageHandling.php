<?php

namespace App\Tools;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ImageHandling
{
    /**
     * Create a new class instance.
     */
    public function __construct() {}

    public static function Base64_to_url(string $base, string $image_folder, int $id)
    {
        $validator = Validator::make([$image_folder], [
            "image_folder" => ['in:Admin,User,Product,Market'],
        ]);
        if ($validator->fails()) {
            return $validator->errors();
        }

        $extension = self::image_extension($base);
        if ($extension == 'jpeg') $extension = 'jpg';
        if (!in_array($extension, ['jpg', 'png', 'gif', 'webp'])) {
            return 'false2';
        }

        $base = substr($base, strpos($base, ',') + 1);
        $image = base64_decode($base);
        if (!$image) {
            return 'false4';
        }

        $image_path = 'images/' . $image_folder . '/' . $id . '.' . $extension;

        Storage::disk('public')->put($image_path, $image);

        $image_url = Storage::url($image_path);
        return env('APP_URL', 'http://programminglanguages.test') . $image_url;
    }
    private static function image_extension(string $base64string)
    {
        $base64string = substr($base64string, strpos($base64string, '/') + 1);
        return explode(';', $base64string)[0];
    }
}
