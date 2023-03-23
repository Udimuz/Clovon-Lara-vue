<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AppointmentStatus;
use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;

class AppointmentsController extends Controller
{
	public function index() {
		$result = Appointment::query()
			->with('client:id,first_name,last_name,email')
			->when(request('status'), function ($query) {
				return $query->where('status', \App\Enums\AppointmentStatus::from(request('status')));
			})
			//->latest()
			->orderBy('id', 'desc')
			->paginate(20)
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
//		$sortedResult = $result->getCollection()->sortByDesc('id')->values();
//		$result->setCollection($sortedResult);
		return $result;
    }

	public function store() {
		$validated = request()->validate([
			'client_id' => 'required',
			'title' => 'required',
			'description' => 'required',
			'start_time' => 'required',
			'end_time' => 'required',
		], [
			'client_id' => 'Необходимо выбрать клиента',
		]);
		Appointment::create([
			'title' => $validated['title'], //request('title'),
			'client_id' => $validated['client_id'], //12,
			'start_time' => $validated['start_time'], //now(),
			'end_time' => $validated['start_time'], //now(),
			'description' => $validated['description'], //request('description'),
			'status' => AppointmentStatus::CONFIRMED,	// SCHEDULED
		]);
		return response()->json(['message' => 'Create success']);
	}
}
