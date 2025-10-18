<?php

namespace App\Services;

use App\Models\Work;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class WorkService
{
    public function getAllWorks(int $perPage = 15): LengthAwarePaginator
    {
        return Work::orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function getAllWorksForAdmin(): LengthAwarePaginator
    {
        return Work::orderBy('id', 'desc')->paginate(15);
    }

    public function createWork(array $data, ?UploadedFile $image = null): Work
    {
        if ($image) {
            $data['image'] = $image->store('works', 'public_direct');
        }

        return Work::create($data);
    }

    public function updateWork(int $id, array $data, ?UploadedFile $image = null): ?Work
    {
        $work = Work::find($id);

        if (!$work) {
            return null;
        }

        if ($image && $image->isValid()) {
            $allowedMimes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

            $fileMime = $image->getMimeType();
            $fileExtension = strtolower($image->getClientOriginalExtension());

            if (in_array($fileMime, $allowedMimes) || in_array($fileExtension, $allowedExtensions)) {
                if ($work->image) {
                    try {
                        Storage::disk('public_direct')->delete($work->image);
                    } catch (\Throwable $e) {
                    }
                }
                $data['image'] = $image->store('works', 'public_direct');
            }
        }

        $work->update($data);
        return $work->fresh();
    }

    public function deleteWork(int $id): bool
    {
        $work = Work::find($id);

        if (!$work) {
            return false;
        }

        if ($work->image) {
            try {
                Storage::disk('public_direct')->delete($work->image);
            } catch (\Throwable $e) {
            }
        }

        return (bool) $work->delete();
    }
}
