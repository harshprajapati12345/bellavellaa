<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'group'];

    public static function get($key, $default = null)
    {
        return \Illuminate\Support\Facades\Cache::remember("setting_$key", 3600, function () use ($key, $default) {
            $setting = self::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    public static function set($key, $value, $group = 'general')
    {
        $setting = self::updateOrCreate(['key' => $key], ['value' => $value, 'group' => $group]);
        \Illuminate\Support\Facades\Cache::forget("setting_$key");
        return $setting;
    }

    public static function getBool(string $key, bool $default = false): bool
    {
        $value = self::get($key, $default ? '1' : '0');

        if (is_bool($value)) {
            return $value;
        }

        return in_array(strtolower((string) $value), ['1', 'true', 'yes', 'on'], true);
    }

    public static function getInt(string $key, int $default = 0): int
    {
        return (int) self::get($key, $default);
    }

    public static function getFloat(string $key, float $default = 0): float
    {
        return (float) self::get($key, $default);
    }
}
