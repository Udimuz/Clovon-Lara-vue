<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index() {
//		$users = User::latest()->get();
//		return $users;
		//return User::orderBy('id')->get();
		return User::all()->sortBy("id");
//		return User::all()->sortBy("id")->map(function($user){
//			return [
//				'id' => $user->id,
//				'name' => $user->name,
//				'email' => $user->email,
//				// 'created_at' => $user->created_at->format(config('app.date_format')),
//				'created_at' => $user->created_at->toFormattedDate(),
//			];
//		});
	}

	public function store() {
		request()->validate([
			'email' => 'required|unique:users,email',
			'name' => 'required|unique:users',	// Эту проверку уже сам добавил, на повтор имени
			'password' => 'required|min:6'
		]);
		return User::create([
			'name' => request('name'),
			'email' => request('email'),
			'password' => bcrypt(request('password'))
		]);
	}

	public function update(User $user) {
		request()->validate([
			// Проверять также как при Добавлении данных, только при проверке на повторение не учитывать текущей записи:
			'email' => 'required|unique:users,email,'.$user->id,
			'name' => 'required',
			'password' => 'sometimes|min:6'
		]);
		$user->update([
			'name' => request('name'),
			'email' => request('email'),
			'password' => request('password') ? bcrypt(request('password')) : $user->password,
		]);
		return $user;
	}

	public function destroy(User $user) {
		$user->delete();
		return response()->noContent();
	}

	public function changeRole(User $user) {
		$user->update([
			'role' => request('role'),
		]);
		return response()->json(['success' => true]);
	}
}
