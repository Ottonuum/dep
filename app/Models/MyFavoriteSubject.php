<?php

namespace App\Models;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;

class MyFavoriteSubject
{
    protected static $cacheKey = 'favorite_subjects';
    protected static $cacheTime = 60; // minutes

    public static function getAll($limit = null)
    {
        return Cache::remember(self::$cacheKey, self::$cacheTime, function () {
            return [];
        });
    }

    public static function find($id)
    {
        $subjects = self::getAll();
        return collect($subjects)->firstWhere('id', $id);
    }

    public static function create($data)
    {
        $subjects = self::getAll();
        $newSubject = array_merge($data, [
            'id' => count($subjects) + 1,
            'created_at' => now()->toDateTimeString(),
            'updated_at' => now()->toDateTimeString()
        ]);
        
        $subjects[] = $newSubject;
        Cache::put(self::$cacheKey, $subjects, self::$cacheTime);
        
        return $newSubject;
    }

    public static function updateSubject($id, $data)
    {
        $subjects = self::getAll();
        $index = collect($subjects)->search(function ($subject) use ($id) {
            return $subject['id'] == $id;
        });

        if ($index !== false) {
            $subjects[$index] = array_merge($subjects[$index], $data, [
                'updated_at' => now()->toDateTimeString()
            ]);
            Cache::put(self::$cacheKey, $subjects, self::$cacheTime);
            return $subjects[$index];
        }

        return null;
    }

    public static function deleteSubject($id)
    {
        $subjects = self::getAll();
        $filteredSubjects = collect($subjects)->filter(function ($subject) use ($id) {
            return $subject['id'] != $id;
        })->values()->all();
        
        Cache::put(self::$cacheKey, $filteredSubjects, self::$cacheTime);
        return true;
    }

    public static function query()
    {
        return new static;
    }

    public function where($field, $operator, $value = null)
    {
        if (func_num_args() === 2) {
            $value = $operator;
            $operator = '=';
        }

        $subjects = self::getAll();
        $filtered = collect($subjects)->filter(function ($subject) use ($field, $operator, $value) {
            switch ($operator) {
                case '=':
                    return $subject[$field] == $value;
                case '>=':
                    return $subject[$field] >= $value;
                case '<=':
                    return $subject[$field] <= $value;
                default:
                    return false;
            }
        });

        return $filtered;
    }

    public function orderBy($field, $direction = 'asc')
    {
        $subjects = self::getAll();
        $sorted = collect($subjects)->sortBy($field, SORT_REGULAR, $direction === 'desc');
        return $sorted->values();
    }

    public function paginate($perPage = 10, $page = 1)
    {
        $subjects = self::getAll();
        $collection = collect($subjects);
        $total = $collection->count();
        $items = $collection->forPage($page, $perPage)->values()->all();

        return new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }
} 