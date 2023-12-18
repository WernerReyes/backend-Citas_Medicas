<?php

namespace App\Http\Controllers;

use App\Helpers\createMedicalAppointmentHistoryHelper;
use App\Helpers\ValidationHelper;
use App\Models\MedicalAppointment;
use App\Models\MedicalAppointmentHistory;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Exception;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $limitPayments = $request->input('limit', 10);
        try {
            $payments = Payment::limit($limitPayments)->get();

            $payments->load('medicalAppointment', 'rateAppointment');

            // Ocultamos los campos que no queremos mostrar
            unset($payments->rate_appointment_id);
            unset($payments->medical_appointment_id);

            return response()->json([
                'status' => 'true',
                'message' => 'Consulta exitosa',
                'payments' => $payments,
                'total_payments' => $payments->count()
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
            $payment = Payment::find($id);
            if ($payment) {
                return response()->json([
                    'status' => 'true',
                    'message' => 'Consulta exitosa',
                    'payment' => $payment
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

    public function earnings(Request $request) {
        try {
            $year = date('Y'); // Obtiene el año actual
            $earnings = [];
    
            // Para cada mes...
            for ($month = 1; $month <= 12; $month++) {
                // Obtiene todos los pagos del mes y año especificados
                $payments = Payment::whereYear('created_at', $year)
                                   ->whereMonth('created_at', $month)
                                   ->get();
    
                $totalEarnings = 0;
    
                // Para cada pago...
                foreach ($payments as $payment) {
                    // ... suma el 'monto' de la cita relacionada a las ganancias totales
                    $totalEarnings += $payment->rateAppointment->monto;
                }
    
                // Añade las ganancias totales al arreglo de ganancias
                $earnings[] = $totalEarnings;
            }
    
            // Devuelve las ganancias de todos los meses
            return response()->json([
                'status' => 'true',
                'message' => 'Consulta exitosa',
                'earnings' => $earnings
            ], 200);
        } catch (Exception $error) {
            return response()->json([
                'status' => 'false',
                'message' => $error->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $rules = [
                'medical_appointment_id' => 'required',
                'user_id' => 'required',
            ];

            $errors = ValidationHelper::validate($request, $rules);

            if ($errors) {
                return $errors;
            }

            $medicalAppointment = MedicalAppointment::find($request->input('medical_appointment_id'));

            if (!$medicalAppointment) {
                return response()->json([
                    'status' => 'false',
                    'message' => 'No se encontró la cita médica'
                ], 404);
            }

            $user = User::find($request->input('user_id'));

            if (!$user) {
                return response()->json([
                    'status' => 'false',
                    'message' => 'No se encontró el usuario'
                ], 404);
            }

            $payment = Payment::create([
                'id' => (string) Str::uuid(),
                'tipo_pago' => $request->input('tipo_pago') ?? 'Tarjeta',
                'medical_appointment_id' => $medicalAppointment->id,
                'rate_appointment_id' => $request->input('rate_appointment_id') ?? 1
            ]);

            // Cambiamos el estado de la cita médica
            $medicalAppointment->estado = 'Cita pagada';
            $medicalAppointment->save();

            // Cambiamos el estado del medico en la cita médica
            $medicalAppointment->estado_medico = 'proceso';
            $medicalAppointment->save();

            //  Guardamos la cita médica en el historial
            createMedicalAppointmentHistoryHelper::class::create(
                $user->id,
                $medicalAppointment->id,
                'Cita pagada',
                'Se ha realizado el pago de la cita médica',
                $payment->id
            );

            return response()->json([
                'status' => 'true',
                'message' => 'Cita médica pagada exitosamente',
                'payment' => $payment
            ], 201);
        } catch (Exception $error) {
            return response()->json([
                'status' => 'false',
                'message' => $error->getMessage()
            ], 500);
        }
    }
}
