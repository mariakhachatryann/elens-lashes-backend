<?php

namespace App\Services;

use App\Models\About;

class AboutService
{
    public function getAbout(): ?About
    {
        return About::first();
    }

    public function saveAbout(array $data): About
    {
        $about = About::first();

        if (!$about) {
            $about = new About();
        }

        if (isset($data['hero_image']) && $data['hero_image'] instanceof \Illuminate\Http\UploadedFile) {
            if ($about->hero_image) {
                try {
                    \Illuminate\Support\Facades\Storage::disk('public_direct')->delete($about->hero_image);
                } catch (\Throwable $e) {
                }
            }
            $data['hero_image'] = $data['hero_image']->store('about', 'public_direct');
        }

        $about->fill($data);
        $about->save();

        return $about;
    }
}

