<?php

namespace App\Models;

use Database\Factories\ClientFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;
	protected $guarded = [];
	protected $table = 'clients';

//	protected static function newFactory() {
//		return new ClientFactory();
		//return \Database\Factories\ClientFactory::class;
//	}
}
