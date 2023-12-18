<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RateAppointment extends Model
{
    use HasFactory;

    protected $table = 'rates_appointments';
    protected $primaryKey = 'id';
    protected $fillable = ['id','monto','descripcion'];

     /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */

}
