<?php

namespace App\Models;

use Illuminate\Support\Facades\Cache;

class Pokemon
{
    private static $cacheKey = 'my_local_pokemons';
    private static $cacheDuration = 60 * 24; // 24 hours

    public static function all()
    {
        return Cache::get(self::$cacheKey, []);
    }

    public static function find($id)
    {
        $pokemons = self::all();
        foreach ($pokemons as $pokemon) {
            if ($pokemon['id'] == $id) {
                return $pokemon;
            }
        }
        return null;
    }

    public static function create(array $data)
    {
        $pokemons = self::all();
        $newId = count($pokemons) > 0 ? max(array_column($pokemons, 'id')) + 1 : 1;
        $newPokemon = array_merge(['id' => $newId], $data);
        $pokemons[] = $newPokemon;
        Cache::put(self::$cacheKey, $pokemons, self::$cacheDuration);
        return (object)$newPokemon; // Return as object to mimic Eloquent
    }

    public static function update($id, array $data)
    {
        $pokemons = self::all();
        foreach ($pokemons as $key => $pokemon) {
            if ($pokemon['id'] == $id) {
                $pokemons[$key] = array_merge($pokemon, $data);
                Cache::put(self::$cacheKey, $pokemons, self::$cacheDuration);
                return (object)$pokemons[$key];
            }
        }
        return null;
    }

    public static function destroy($id)
    {
        $pokemons = self::all();
        $updatedPokemons = array_filter($pokemons, function ($pokemon) use ($id) {
            return $pokemon['id'] != $id;
        });
        Cache::put(self::$cacheKey, array_values($updatedPokemons), self::$cacheDuration);
        return true;
    }

    public static function where($field, $value)
    {
        $pokemons = self::all();
        return array_filter($pokemons, function ($pokemon) use ($field, $value) {
            return isset($pokemon[$field]) && strtolower($pokemon[$field]) == strtolower($value);
        });
    }

    public static function exists($field, $value)
    {
        return count(self::where($field, $value)) > 0;
    }

    public static function pluck($field)
    {
        $pokemons = self::all();
        return array_column($pokemons, $field);
    }
} 