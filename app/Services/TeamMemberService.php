<?php

namespace App\Services;

use App\Models\TeamMember;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;

class TeamMemberService
{
    public function getAll(int $perPage = 50): LengthAwarePaginator
    {
        return TeamMember::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->paginate($perPage);
    }

    public function getAllForAdmin(): LengthAwarePaginator
    {
        return TeamMember::orderBy('sort_order')
            ->orderByDesc('id')
            ->paginate(20);
    }

    public function create(array $data, ?UploadedFile $image = null): TeamMember
    {
        if ($image) {
            $data['image'] = $image->store('team', 'public_direct');
        }

        if (!isset($data['sort_order'])) {
            $data['sort_order'] = 0;
        }

        return TeamMember::create($data);
    }

    public function update(int $id, array $data, ?UploadedFile $image = null): ?TeamMember
    {
        $member = TeamMember::find($id);

        if (!$member) {
            return null;
        }

        if ($image && $image->isValid()) {
            $allowedMimes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

            $fileMime = $image->getMimeType();
            $fileExtension = strtolower($image->getClientOriginalExtension());

            if (in_array($fileMime, $allowedMimes) || in_array($fileExtension, $allowedExtensions)) {
                if ($member->image) {
                    try {
                        Storage::disk('public_direct')->delete($member->image);
                    } catch (\Throwable $e) {
                    }
                }
                $data['image'] = $image->store('team', 'public_direct');
            }
        }

        $member->update($data);

        return $member->fresh();
    }

    public function delete(int $id): bool
    {
        $member = TeamMember::find($id);

        if (!$member) {
            return false;
        }

        if ($member->image) {
            try {
                Storage::disk('public_direct')->delete($member->image);
            } catch (\Throwable $e) {
            }
        }

        return (bool) $member->delete();
    }
}

