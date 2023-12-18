<?php

namespace App\Http\Controllers;

use App\Helpers\ValidationHelper;
use App\Models\Doctor;
use App\Models\MedicalSchedule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Exception;
use Faker\Provider\Medical;

class MedicalScheduleController extends Controller
{
    public function index(Request $request)
    {
        $limitSchedules = $request->input('limit', 10);
        $idDoctor = $request->input('doctor_id', null);
        $fecha = $request->input('fecha', null);
        try {
            $query = MedicalSchedule::query();

            if ($idDoctor) {
                $query->where('doctor_id', $idDoctor);
            }

            if ($fecha) {
                $query->where('fecha', $fecha);
            }

            // Solo trae los horarios activos
            $query->where('activo', true);

            // Solo trae los horarios cuyo doctor también está activo y cuya especialidad también está activa
            $query->whereHas('doctor', function ($query) {
                $query->where('activo', true)
                    ->whereHas('specialy', function ($query) {
                        $query->where('activo', true);
                    });
            });

            $schedules = $query->limit($limitSchedules)->get();

            $schedules->load('doctor');

            return response()->json([
                'status' => 'true',
                'message' => 'Consulta exitosa',
                'schedules' => $schedules,
                'total_schedules' => $schedules->count()
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
            $schedule = MedicalSchedule::find($id);

            if (!$schedule) {
                return response()->json([
                    'status' => 'false',
                    'message' => 'Horario no encontrado'
                ], 404);
            }

            return response()->json([
                'status' => 'true',
                'message' => 'Consulta exitosa',
                'schedule' => $schedule
            ], 200);
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
                'hora_inicio' => 'required|date_format:H:i',
                'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
                'doctor_id' => 'required'
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'false',
                    'message' => $validator->errors()
                ], 400);
            }

            $horaInicio = Carbon::createFromFormat('H:i', $request->hora_inicio);
            $horaFin = Carbon::createFromFormat('H:i', $request->hora_fin);
            $diferenciaMinutos = $horaInicio->diffInMinutes($horaFin);

            if (!in_array($diferenciaMinutos, [30, 45, 60])) {
                return response()->json([
                    'status' => 'false',
                    'message' => 'La diferencia entre la hora de inicio y fin debe ser 30, 45 o 60 minutos'
                ], 422);
            }

            $scheduleExists = MedicalSchedule::where('doctor_id', $request->doctor_id)
                ->where('fecha', $request->fecha)
                ->where('hora_inicio', $request->hora_inicio)
                ->where('hora_fin', $request->hora_fin)
                ->exists();

            if ($scheduleExists) {
                return response()->json([
                    'status' => 'false',
                    'message' => 'El doctor ya tiene un horario asignado con la misma hora de inicio y fin'
                ], 422);
            }

            $schedule = MedicalSchedule::create([
                'id' => (string) Str::uuid(),
                'fecha' => $request->fecha,
                'hora_inicio' => $request->hora_inicio,
                'hora_fin' => $request->hora_fin,
                'doctor_id' => $request->doctor_id
            ]);

            $schedule->load('doctor');

            return response()->json([
                'status' => 'true',
                'message' => 'Horario creado exitosamente',
                'schedule' => $schedule
            ], 201);
        } catch (Exception $error) {
            Log::error($error->getMessage());
            return response()->json([
                'status' => 'false',
                'message' => 'Horario no creado',
                'error' => $error->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $schedule = MedicalSchedule::find($id);

            if (!$schedule) {
                return response()->json([
                    'status' => 'false',
                    'message' => 'Horario no encontrado'
                ], 404);
            }

            $rules = [
                'fecha' => 'required',
                'hora_inicio' => 'required',
                'hora_fin' => 'required',
                'doctor_id' => 'required'
            ];

            $errors = ValidationHelper::validate($request, $rules);

            if ($errors) {
                return $errors;
            }

            $schedule->fecha = $request->fecha;
            $schedule->hora_inicio = $request->hora_inicio;
            $schedule->hora_fin = $request->hora_fin;
            $schedule->doctor_id = $request->doctor_id;
            $schedule->save();

            $schedule->load('doctor');

            return response()->json([
                'status' => 'true',
                'message' => 'Horario actualizado exitosamente',
                'schedule' => $schedule
            ], 200);
        } catch (Exception $error) {
            Log::error($error->getMessage());
            return response()->json([
                'status' => 'false',
                'message' => 'Horario no actualizado'
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $schedule = MedicalSchedule::find($id);

            if (!$schedule) {
                return response()->json([
                    'status' => 'false',
                    'message' => 'Horario no encontrado'
                ], 404);
            }

            $schedule->activo = false;
            $schedule->save();

            return response()->json([
                'status' => 'true',
                'message' => 'Horario eliminado exitosamente',
                'schedule' => $schedule
            ], 200);
        } catch (Exception $error) {
            Log::error($error->getMessage());
            return response()->json([
                'status' => 'false',
                'message' => 'Horario no eliminado'
            ], 500);
        }
    }
}
