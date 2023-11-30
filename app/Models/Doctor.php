<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Role;
use Laravel\Sanctum\HasApiTokens;

class Doctor extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $table = 'doctors';
    protected $primaryKey = 'id';
    protected $fillable = ['id','nombre', 'apellido', 'correo', 'password', 'direccion','dni','telefono','rol_id', 'especialidad_id','create_at','update_at'];

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    public function specialy()
    {
        return $this->belongsTo(Specialty::class, 'especialidad_id');
    }

    public function rol()
    {
        return $this->belongsTo(Role::class, 'rol_id');
    }
  
}
