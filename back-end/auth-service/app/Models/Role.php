<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    // Mengizinkan kolom 'name' diisi secara otomatis
    protected $fillable = ['name'];

    // Relasi: Satu Role bisa punya banyak User
    public function users()
    {
        return $this->hasMany(User::class);
    }
}