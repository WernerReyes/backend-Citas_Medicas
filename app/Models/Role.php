<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Role extends Model
{
    protected $table = 'rols';
    protected $primaryKey = 'id';
    protected $fillable = ['id','nombre'];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
