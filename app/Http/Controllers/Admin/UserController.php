<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index() {
//		$users = User::latest()->get();
//		$users = User::latest()->paginate(3);
//		return $users;
		//return User::orderBy('id')->get();
//		$users = User::all()->sortBy("id");
		// dd($users[0]);
		$users = User::orderBy('id', 'asc')->paginate(3);
		return $users;
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
//		return User::create([
//			'name' => request('name'),
//			'email' => request('email'),
//			'password' => bcrypt(request('password'))
//		]);
		//$user =  User::create([
		return User::create([
			'name' => request('name'),
			'email' => request('email'),
			'password' => bcrypt(request('password')),
			'role' => 2		// При добавлении новой записи, потом не возвращает что вставилось, параметр из таблицы (что по умолчанию) не возвращает
			// Приходится вручную указывать, чтобы по умолчанию выводил, как в базе указано, т.е. "USER"
		]);
//		dd($user->role);
		//$user->role = 2;
//		if (isset($user->role))
//			dd("===");
//		dd("=======-rollll:{$user->role}:");
//		unset($user->role);
//		$user->name = "Dima 222";
		//dd($user->email_verified_at);
		//return $user;
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

	public function search() {
		$searchQuery = request('query');
		// $users = User::where('name', 'like', "%{$searchQuery}%")->get();
		$users = User::where('name', 'like', "%{$searchQuery}%")->paginate(3);
		return response()->json($users);
	}

}
