<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalAppointment extends Model
{
    use HasFactory;

    protected $table = 'medical_appointments';
    protected $primaryKey = 'id';
    protected $fillable = ['id','fecha', 'sede', 'estado', 'estado_medico', 'paciente_id', 'doctor_id', 'schedule_id', 'rate_appointment_id', 'create_at','update_at'];

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

    public function schedule()
    {
        return $this->belongsTo(MedicalSchedule::class, 'schedule_id');
    }

    public function patient()
    {
        return $this->belongsTo(User::class, 'paciente_id');
    }


    public function doctor()
    {
        return $this->belongsTo(Doctor::class, 'doctor_id');
    }
}
