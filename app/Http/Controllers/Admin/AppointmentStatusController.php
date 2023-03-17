<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AppointmentStatus;
use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;

class AppointmentStatusController extends Controller
{
	public function getStatusWithCount()
	{
//		return [
//			[ 'name' => 'scheduled' ],
//			[ 'name' => 'confirmed' ],
//			[ 'name' => 'cancelled' ],
//		];
		$cases = AppointmentStatus::cases();
		return collect($cases)->map(function ($status){
			return [
				'name' => ucfirst(mb_strtolower($status->name)),	// сделаем преобразование регистра слов более красивым
				'value' => $status->value,
				'count' => Appointment::where('status', $status->value)->count(),
				'color' => AppointmentStatus::from($status->value)->color(),
			];
		});
    }
}
