<?php

namespace App\Models\CRM;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrmAppSetting extends Model
{
    use HasFactory;

    protected $table = 'crm_app_settings';

    protected $fillable = [
        'app_name',
        'app_logo',
        'login_logo',
        'splash_logo',
        'default_profile_image',
        'primary_color',
        'secondary_color',
    ];

    public static function firstOrCreateDefault(): self
    {
        return static::firstOrCreate([], [
            'app_name' => "Sane's Academy",
            'primary_color' => '#0F5917',
            'secondary_color' => '#14802C',
        ]);
    }

    public static function publicUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return asset($path);
    }

    public function toMobileArray(): array
    {
        return [
            'app_name' => $this->app_name,
            'logo_url' => static::publicUrl($this->app_logo),
            'app_logo_url' => static::publicUrl($this->app_logo),
            'login_logo_url' => static::publicUrl($this->login_logo ?: $this->app_logo),
            'splash_logo_url' => static::publicUrl($this->splash_logo ?: $this->app_logo),
            'default_profile_image_url' => static::publicUrl($this->default_profile_image),
            'primary_color' => $this->primary_color,
            'secondary_color' => $this->secondary_color,
        ];
    }
}
