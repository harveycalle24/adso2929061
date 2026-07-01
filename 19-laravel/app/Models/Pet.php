<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pet extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'image',
        'kind',
        'weight',
        'age',
        'breed',
        'location',
        'description',
        'active',
        'adopted'
    ];

    // Casts para convertir automáticamente los tipos de datos
    protected $casts = [
        'active' => 'boolean',
        'adopted' => 'boolean',
        'weight' => 'float',
        'age' => 'integer',
    ];

    /**
     * Scope para búsqueda por nombre (mejorado)
     */
    public function scopeNames($query, $q) 
    {
        if ($q) {
            return $query->where('name', 'LIKE', '%' . $q . '%')
                        ->orWhere('breed', 'LIKE', '%' . $q . '%')
                        ->orWhere('kind', 'LIKE', '%' . $q . '%')
                        ->orWhere('location', 'LIKE', '%' . $q . '%');
        }
        return $query;
    }

    /**
     * Scope para filtrar por estado de adopción
     */
    public function scopeAdopted($query, $status = true) 
    {
        return $query->where('adopted', $status);
    }

    /**
     * Scope para filtrar por estado activo
     */
    public function scopeActive($query, $status = true) 
    {
        return $query->where('active', $status);
    }

    /**
     * Accesor para obtener el estado de adopción como texto
     */
    public function getAdoptionStatusAttribute()
    {
        if ($this->adopted) {
            return 'Adopted';
        }
        return $this->active ? 'Available' : 'Inactive';
    }

    /**
     * Accesor para obtener el estado formateado con badge
     */
    public function getStatusBadgeAttribute()
    {
        if ($this->adopted) {
            return '<span class="badge bg-danger">Adopted</span>';
        }
        if ($this->active) {
            return '<span class="badge bg-success">Available</span>';
        }
        return '<span class="badge bg-warning">Inactive</span>';
    }

    /**
     * Accesor para obtener la URL de la imagen
     */
    public function getImageUrlAttribute()
    {
        if ($this->image && $this->image != 'no-image.png' && $this->image != 'images/pets/no-image.png') {
            if (file_exists(public_path($this->image))) {
                return asset($this->image);
            }
        }
        return asset('images/pets/no-image.png');
    }

    /**
     * Mutador para asegurar que active siempre sea booleano
     */
    public function setActiveAttribute($value)
    {
        $this->attributes['active'] = $value ? 1 : 0;
    }

    /**
     * Mutador para asegurar que adopted siempre sea booleano
     */
    public function setAdoptedAttribute($value)
    {
        $this->attributes['adopted'] = $value ? 1 : 0;
    }
}