<?php

namespace App\Services;

use App\Models\Service;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class ServiceService
{
    public function getAllServices(int $perPage = 15): LengthAwarePaginator
    {
        return Service::with('children')
            ->whereNull('parent_id')
            ->orderBy('id', 'desc')
            ->paginate($perPage);
    }

    public function getAllServicesForAdmin(): LengthAwarePaginator
    {
        return Service::with('children')
            ->whereNull('parent_id')
            ->orderBy('id', 'desc')
            ->paginate(15);
    }

    public function getServiceById(int $id): ?Service
    {
        return Service::with(['children', 'parent'])->find($id);
    }

    public function createService(array $data, ?UploadedFile $image = null): Service
    {
        if ($image) {
            $data['image'] = $image->store('services', 'public_direct');
        }

        return Service::create($data);
    }

    public function updateService(int $id, array $data, ?UploadedFile $image = null): ?Service
    {
        $service = Service::find($id);

        if (!$service) {
            return null;
        }

        if ($image && $image->isValid()) {
            $allowedMimes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

            $fileMime = $image->getMimeType();
            $fileExtension = strtolower($image->getClientOriginalExtension());

            if (!in_array($fileMime, $allowedMimes) && !in_array($fileExtension, $allowedExtensions)) {
                throw new \InvalidArgumentException('Invalid file type. Please upload a JPEG, PNG, GIF or WEBP image.');
            }

            if ($service->image) {
                try {
                    Storage::disk('public_direct')->delete($service->image);
                } catch (\Throwable $e) {
                }
            }

            $data['image'] = $image->store('services', 'public_direct');
        }

        $service->update($data);
        return $service->fresh();
    }

    public function deleteService(int $id): bool
    {
        $service = Service::find($id);

        if (!$service) {
            return false;
        }

        if ($service->image) {
            try {
                Storage::disk('public_direct')->delete($service->image);
            } catch (\Throwable $e) {
            }
        }

        return (bool) $service->delete();
    }
}
