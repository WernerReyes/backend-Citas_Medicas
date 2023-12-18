<?php

namespace App\Console\Commands;

use App\Helpers\createMedicalAppointmentHistoryHelper;
use Illuminate\Console\Command;
use App\Models\MedicalAppointment;
use Carbon\Carbon;

class UpdateAppointmentStatus extends Command
{
    protected $signature = 'update:appointment-status';

    protected $description = 'Update the status of appointments that have not been paid after 24 hours';

    public function handle()
    {
        $appointments = MedicalAppointment::where('estado', 'pendiente')
            ->where(Carbon::parse('fecha'), '<', Carbon::now()->subHours(24))
            ->get();

        foreach ($appointments as $appointment) {
            $appointment->estado = 'expirado por falta de pago';
            $appointment->save();
        }

        // Si el estado de la cita no es "pendiente" guardamos en la tabla de historial de citas
        if($appointment->estado != 'pendiente'){
            createMedicalAppointmentHistoryHelper::create(
                $appointment->paciente_id,
                $appointment->id,
                $appointment->estado,
                'Cita expirada por falta de pago'
            );
        }

    }
}