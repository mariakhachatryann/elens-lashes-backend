<?php

namespace App\Services;

use App\Models\Classes;
use Illuminate\Pagination\LengthAwarePaginator;

class ClassService
{
    public function getAllClasses(int $perPage = 15): LengthAwarePaginator
    {
        return Classes::paginate($perPage);
    }

    public function getAllClassesForAdmin(): LengthAwarePaginator
    {
        return Classes::paginate(15);
    }

    public function createClass(array $data): Classes
    {
        return Classes::create($data);
    }

    public function updateClass(int $id, array $data): ?Classes
    {
        $class = Classes::find($id);
        
        if (!$class) {
            return null;
        }

        $class->update($data);
        return $class->fresh();
    }

    public function deleteClass(int $id): bool
    {
        $class = Classes::find($id);
        
        if (!$class) {
            return false;
        }

        return $class->delete();
    }
}


