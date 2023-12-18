<?php

namespace App\Helpers;

use App\Models\MedicalAppointmentHistory;
use Illuminate\Support\Str;

class createMedicalAppointmentHistoryHelper
{
    public static function create($user_id, $medical_appointment_id, $status, $description, $payment_id=null, $medical_status=null)
    {
        $medical_appointment_history = new MedicalAppointmentHistory();
        $medical_appointment_history->id = Str::uuid();
        $medical_appointment_history->user_id = $user_id;
        $medical_appointment_history->medical_appointment_id = $medical_appointment_id;
        $medical_appointment_history->status = $status;
        $medical_appointment_history->medical_status = $medical_status;
        $medical_appointment_history->payment_id = $payment_id;
        $medical_appointment_history->description = $description;
        $medical_appointment_history->save();
    }
   
      
}