<?php

namespace App\Http\Controllers;

use App\Helpers\createMedicalAppointmentHistoryHelper;
use App\Helpers\ValidationHelper;
use App\Models\Doctor;
use App\Models\MedicalAppointment;
use App\Models\MedicalAppointmentHistory;
use App\Models\MedicalSchedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Exception;

class MedicalAppointmentController extends Controller
{
    public function index(Request $request)
    {
        $limitSchedules = $request->input('limit', 10);
        $estado = $request->input('estado', null);
        $estadoMedico = $request->input('estadoMedico', null);
        $userId = $request->input('userId', null);
        $doctorId = $request->input('doctorId', null);
        // $pendingAppointment = $request->input('pending', false);
        try {
            $query = MedicalAppointment::query();

            // Si el estado es pendiente, se filtran las citas médicas que no estén pagadas ni anuladas
            if ($estado) {
                $query->where('estado', $estado);
            }

            if ($estadoMedico) {
                $query->where('estado_medico', $estadoMedico);
            }

            // Si el usuario es un paciente, se filtran las citas médicas que le pertenezcan
            if ($userId) {
                $query->where('paciente_id', $userId);
            }

            if ($doctorId) {
                $query->where('doctor_id', $doctorId);
            }

            // Solo trae las citas médicas activas
            $query->where('activo', true);

            $appointments = $query->latest()->limit($limitSchedules)->get();

            $appointments->load('schedule', 'patient', 'doctor', 'doctor.specialy');

            // Ocultamos los campos que no queremos mostrar
            unset($appointments->paciente_id);
            unset($appointments->schedule_id);
            unset($appointments->doctor_id);


            return response()->json([
                'status' => 'true',
                'message' => 'Consulta exitosa',
                'appointments' => $appointments,
                'total_schedules' => $appointments->count()
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
            $appointment = MedicalAppointment::find($id);

            $appointment->load('schedule', 'patient', 'doctor', 'doctor.specialy');

            // Ocultamos los campos que no queremos mostrar
            unset($appointment->paciente_id);
            unset($appointment->schedule_id);
            unset($appointment->doctor_id);


            if ($appointment) {
                return response()->json([
                    'status' => 'true',
                    'message' => 'Consulta exitosa',
                    'appointment' => $appointment
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

    public function store(Request $request)
    {
        try {

            $rules = [
                'fecha' => 'required|date|after_or_equal:today',
                'paciente_id' => 'required|string',
                'sede' => 'required|string',
                'doctor_id' => 'required|string',
                'schedule_id' => 'required|string',
            ];

            $errors = ValidationHelper::validate($request, $rules);

            if ($errors) {
                return $errors;
            }

            $paciente = User::find($request->input('paciente_id'));
            if (!$paciente) {
                return response()->json([
                    'status' => 'false',
                    'message' => 'No se encontró el paciente'
                ], 404);
            }

            $doctor = Doctor::find($request->input('doctor_id'));
            if (!$doctor) {
                return response()->json([
                    'status' => 'false',
                    'message' => 'No se encontró el doctor'
                ], 404);
            }

            $schedule = MedicalSchedule::find($request->input('schedule_id'));
            if (!$schedule) {
                return response()->json([
                    'status' => 'false',
                    'message' => 'No se encontró el horario'
                ], 404);
            }

            $appointment = MedicalAppointment::create([
                'id' => (string) Str::uuid(),
                'fecha' => $request->input('fecha'),
                'sede' => $request->input('sede'),
                'paciente_id' => $request->input('paciente_id'),
                'doctor_id' => $request->input('doctor_id'),
                'schedule_id' => $request->input('schedule_id'),
                "estado" => "pendiente",
            ]);

            // Actualizar el estado del horario
            $schedule->disponible = false;
            $schedule->save();

            $appointment->load('schedule', 'patient', 'doctor', 'doctor.specialy');

            // Ocultamos los campos que no queremos mostrar
            // unset($appointment->paciente_id);
            // unset($appointment->schedule_id);
            // unset($appointment->doctor_id);

            return response()->json([
                'status' => 'true',
                'message' => 'Cita médica creada exitosamente',
                'appointment' => $appointment
            ], 201);
        } catch (Exception $error) {
            return response()->json([
                'status' => 'false',
                'message' => $error->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $appointment = MedicalAppointment::find($id);
            if ($appointment) {

                $rules = [
                    'fecha' => 'required|date',
                    'paciente_id' => 'required|string',
                    'sede' => 'required|string',
                    'doctor_id' => 'required|string',
                    'schedule_id' => 'required|string',
                ];

                $errors = ValidationHelper::validate($request, $rules);

                if ($errors) {
                    return $errors;
                }


                // Encuentramos el horario asociado con la cita antes de actualizarla
                $oldSchedule = MedicalSchedule::find($appointment->schedule_id);
                if ($oldSchedule) {
                    // Cambia el estado del horario antiguo a disponible
                    $oldSchedule->disponible = true;
                    $oldSchedule->save();
                }

                $paciente = User::find($request->input('paciente_id'));
                if (!$paciente) {
                    return response()->json([
                        'status' => 'false',
                        'message' => 'No se encontró el paciente'
                    ], 404);
                }

                $doctor = Doctor::find($request->input('doctor_id'));
                if (!$doctor) {
                    return response()->json([
                        'status' => 'false',
                        'message' => 'No se encontró el doctor'
                    ], 404);
                }

                $schedule = MedicalSchedule::find($request->input('schedule_id'));
                if (!$schedule) {
                    return response()->json([
                        'status' => 'false',
                        'message' => 'No se encontró el horario'
                    ], 404);
                }

                // Convertir la fehca de la cita médica a Carbon
                $appointmentDate = Carbon::parse($appointment->fecha);

                // Verificamos de no haya pasado 24 horas desde la creación de la cita médica
                if (!$appointmentDate->gt(Carbon::now()->addDay())) {

                    if ($appointment->estado != 'pendiente') {
                        return response()->json([
                            'status' => 'false',
                            'message' => 'No se puede editar una cita médica que ya ha sido pagada o anulada'
                        ], 403);
                    }

                    // Actualizar el estado del horario
                    $schedule->disponible = false;
                    $schedule->save();

                    $appointment->load('schedule', 'patient', 'doctor', 'doctor.specialy');


                    $appointment->update($request->all());
                    return response()->json([
                        'status' => 'true',
                        'message' => 'Cita médica actualizada exitosamente',
                        'appointment' => $appointment
                    ], 200);
                }

                return response()->json([
                    'status' => 'false',
                    'message' => 'Solo se pueden editar citas médicas que sean al menos 24 horas en el futuro'
                ], 403);
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

    public function complete(Request $request, $id)
    {
        try {
            $appointment = MedicalAppointment::find($id);
            if ($appointment) {
                $appointment->estado_medico = 'Cita completada';
                $appointment->save();

                $appointmentHistory = MedicalAppointmentHistory::where('medical_appointment_id', $appointment->id)->first();

                if (!$appointmentHistory) {
                    return response()->json([
                        'status' => 'false',
                        'message' => 'No se encontró el historial de la cita médica'
                    ], 404);
                }

                // Guardar la cita médica en el historial de citas
                $appointmentHistory->medical_status = 'Cita completada';
                $appointmentHistory->save();

                $appointment->load('schedule', 'patient', 'doctor', 'doctor.specialy');

                return response()->json([
                    'status' => 'true',
                    'message' => 'Cita médica completada exitosamente',
                    'appointment' => $appointment
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
        } catch (Exception $error) {
            return response()->json([
                'status' => 'false',
                'message' => $error->getMessage()
            ], 500);
        }
    }

    public function destroy(Request $request, $id, $idSchedule)
    {
        try {
            $schedule = MedicalSchedule::find($idSchedule);

            if (!$schedule) {
                return response()->json([
                    'status' => 'false',
                    'message' => 'No se encontró el horario'
                ], 404);
            }

            $appointment = MedicalAppointment::find($id);
            if ($appointment) {
                // Convertir la fehca de la cita médica a Carbon
                $appointmentDate = Carbon::parse($appointment->fecha);

                // Verificamos de no haya pasado 24 horas desde la creación de la cita médica
                if ($appointmentDate->gt(Carbon::now()->addDay())) {
                    $appointment->estado = 'Cita anulada';
                    $appointment->activo = false;
                    $appointment->save();

                    // Actualizar el estado del horario
                    $schedule->disponible = true;
                    $schedule->save();

                    // Guardar la cita médica en el historial de citas
                    createMedicalAppointmentHistoryHelper::create(
                        $appointment->paciente_id,
                        $appointment->id,
                        'Cita anulada',
                        'Cita anulada por el paciente'
                    );

                    return response()->json([
                        'status' => 'true',
                        'message' => 'Cita médica cancelada exitosamente'
                    ], 200);
                } else {
                    return response()->json([
                        'status' => 'false',
                        'message' => 'Solo se pueden eliminar citas médicas que sean al menos 24 horas en el futuro'
                    ], 403);
                }
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
