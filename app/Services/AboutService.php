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

        $about->fill([
            'title' => $data['title'] ?? $about->title,
            'description' => $data['description'] ?? $about->description,
        ]);

        $about->save();

        return $about;
    }
}

