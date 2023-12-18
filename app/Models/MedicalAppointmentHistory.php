<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalAppointmentHistory extends Model
{
    use HasFactory;


    protected $table = 'medical_appointment_history';
    protected $primaryKey = 'id';
    protected $fillable = ['id','status', 'user_id', 'medical_appointment_id', 'payment_id','create_at','update_at'];

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

    public function payment()
    {
        return $this->belongsTo(Payment::class, 'payment_id');
    }

    public function patient()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function medicalAppointment()
    {
        return $this->belongsTo(MedicalAppointment::class, 'medical_appointment_id');
    } 
}
