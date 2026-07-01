<?php
// app/Models/User.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'document',
        'fullname',
        'gender',
        'birthdate',
        'photo',
        'phone',
        'email',
        'password',
        'active',
        'role'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function adoptions()
    {
        return $this->hasMany(Adoption::class);
    }

    public function pet()
    {
        return $this->hasOne(Pet::class);
    }

    public function scopeNames($users, $q) {
        if (trim($q) != "") {
            $users->where('fullname', 'like', "%$q%")
                ->orWhere('email', 'like', "%$q%")
                ->orWhere('document', 'like', "%$q%");
        }
        return $users;
    }

    // 🔥 ACCESOR MEJORADO PARA LA FOTO
    public function getPhotoUrlAttribute()
    {
        // Si la foto está vacía
        if (empty($this->photo)) {
            return asset('images/users/no-photo.png');
        }

        // Si la foto es 'no-photo.png' (sin ruta)
        if ($this->photo == 'no-photo.png') {
            return asset('images/users/no-photo.png');
        }

        // Si la foto ya tiene la ruta completa y existe
        if (str_contains($this->photo, 'images/')) {
            if (file_exists(public_path($this->photo))) {
                return asset($this->photo);
            }
        }

        // Si la foto solo tiene el nombre (ej: 'jhonwick.jpeg')
        // Buscar en diferentes carpetas
        $possiblePaths = [
            'images/' . $this->photo,
            'images/users/' . $this->photo,
            'images/pets/' . $this->photo,
            $this->photo
        ];

        foreach ($possiblePaths as $path) {
            if (file_exists(public_path($path))) {
                // Actualizar la ruta en la base de datos para futuras consultas
                $this->photo = $path;
                $this->save();
                return asset($path);
            }
        }

        // Si no se encuentra ninguna imagen
        return asset('images/users/no-photo.png');
    }

    // Mutador para guardar la foto correctamente
    public function setPhotoAttribute($value)
    {
        if (empty($value)) {
            $this->attributes['photo'] = 'images/users/no-photo.png';
        } elseif ($value == 'no-photo.png') {
            $this->attributes['photo'] = 'images/users/no-photo.png';
        } elseif (!str_contains($value, 'images/')) {
            // Si solo es el nombre del archivo, agregar la ruta
            $this->attributes['photo'] = 'images/users/' . $value;
        } else {
            $this->attributes['photo'] = $value;
        }
    }
}