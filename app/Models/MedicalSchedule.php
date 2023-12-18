<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalSchedule extends Model
{
    use HasFactory;

    protected $table = 'medical_schedules';
    protected $primaryKey = 'id';
    protected $fillable = ['id','fecha', 'hora_inicio', 'hora_fin', 'doctor_id','create_at','update_at'];

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


    public function doctor()
    {
        return $this->belongsTo(Doctor::class, 'doctor_id');
    }
}
