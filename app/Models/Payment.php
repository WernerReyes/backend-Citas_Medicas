<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $table = 'payments';
    protected $primaryKey = 'id';
    protected $fillable = ['id','tipo_pago', 'medical_appointment_id', 'rate_appointment_id','create_at','update_at'];

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

    public function medicalAppointment()
    {
        return $this->belongsTo(MedicalAppointment::class);
    }

    public function rateAppointment()
    {
        return $this->belongsTo(RateAppointment::class);
    }
}
