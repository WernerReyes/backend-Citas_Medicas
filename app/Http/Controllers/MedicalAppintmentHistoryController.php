<?php

namespace App\Http\Controllers;

use App\Models\MedicalAppointmentHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Exception;

class MedicalAppintmentHistoryController extends Controller
{
    public function index(Request $request)
    {
        $limitSchedules = $request->input('limit', 10);
        $idUser = $request->input('user_id', null);
        $idDoctor = $request->input('doctor_id', null);
        try {
            
            $query = MedicalAppointmentHistory::query();

            if ($idUser) {
                $query->where('user_id', $idUser);
            }

            if ($idDoctor) {
                $query->whereHas('medicalAppointment', function ($query) use ($idDoctor) {
                    $query->where('doctor_id', $idDoctor);
                });
            }

            $historial = $query->limit($limitSchedules)->get();
            
            $historial->load('medicalAppointment', 'patient', 'payment', 'medicalAppointment.doctor', 'payment.rateAppointment', 'medicalAppointment.doctor.specialy');

            // Ocultamos los campos que no queremos mostrar
            unset($historial->user_id);
            unset($historial->medical_appointment_id);
            unset($historial->payment_id);

            return response()->json([
                'status' => 'true',
                'message' => 'Consulta exitosa',
                'historial' => $historial,
                'total_schedules' => $historial->count()
            ], 200);
        } catch (Exception $error) {
            return response()->json([
                'status' => 'false',
                'message' => $error->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $historial = MedicalAppointmentHistory::find($id);
            if ($historial) {

                $historial->load('medicalAppointment', 'patient', 'payment','medicalAppointment.doctor', 'payment.rateAppointment', 'medicalAppointment.doctor.specialy');

                // Ocultamos los campos que no queremos mostrar
                unset($historial->user_id);
                unset($historial->medical_appointment_id);
                unset($historial->payment_id);
    
                return response()->json([
                    'status' => 'true',
                    'message' => 'Consulta exitosa',
                    'historial' => $historial
                ], 200);
            } else {
                return response()->json([
                    'status' => 'false',
                    'message' => 'No se encontró la cita médica'
                ], 404);
            }
        } catch (Exception $error) {
            return response()->json([
                'status' => 'false',
                'message' => $error->getMessage()
            ], 500);
        }
    }
}
