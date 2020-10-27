<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Login extends Model
{
    protected $table = 'user_login';
	protected $fillable = ["forgot_time","forgot_url"];
}
