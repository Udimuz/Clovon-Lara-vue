<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ClientController extends Controller
{
	public function index()
	{
		return \App\Models\Client::latest()->get();
    }
}
