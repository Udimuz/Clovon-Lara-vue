<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;

class AppointmentsController extends Controller
{
	public function index()
	{
		return Appointment::query()
			->with('client:id,first_name,last_name,email')
			->when(request('status'), function ($query) {
				return $query->where('status', \App\Enums\AppointmentStatus::from(request('status')));
			})
			->latest()
			->paginate()
			// Для преобразования (форматирования) данных добавили такую конструкцию:
			->through(fn ($appoinment) => [
				'id' => $appoinment->id,
				'start_time' => $appoinment->start_time->format('d.m.Y h:i A'),
				'end_time' => $appoinment->end_time->format('d.m.Y h:i A'),
				// 'status' => $appoinment->status,
				'status' => [
					'name' => $appoinment->status->name,
					'color' => $appoinment->status->color(),
				],
				'client' => $appoinment->client,
			]);
    }
}
