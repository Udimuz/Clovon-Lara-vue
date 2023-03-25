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
				'name' => ucfirst(mb_strtolower($status->name)),	// сделаем преобразование регистра слов более красивым. Первая буква будет в верхнем регистре, остальные маленькие
				'value' => $status->value,
				'count' => Appointment::where('status', $status->value)->count(),
				'color' => AppointmentStatus::from($status->value)->color(),
			];
		});
    }

	public function getStatuses()
	{
		$cases = AppointmentStatus::cases();
		return collect($cases)->map(function ($status){
			return [
				'name' => $status->name,
				'value' => $status->value,
			];
		});
//		$users = User::latest()->get();
//		$users = User::latest()->paginate(3);
//		return $users;
		//return User::orderBy('id')->get();
//		$users = User::all()->sortBy("id");
	}
}
