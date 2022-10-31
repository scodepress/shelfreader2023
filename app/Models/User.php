<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\DB;
use Lab404\Impersonate\Models\Impersonate;
use Illuminate\Contracts\Auth\CanResetPassword;

class User extends \TCG\Voyager\Models\User
{
	use HasApiTokens;
	use HasFactory;
	use HasProfilePhoto;
	use Notifiable;
	use TwoFactorAuthenticatable;
	use Impersonate;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var string[]
	 */
	protected $fillable = [
		'name',
		'email',
		'password',
	];

	/**
	 * The attributes that should be hidden for serialization.
	 *
	 * @var array
	 */
	protected $hidden = [
		'password',
		'remember_token',
		'two_factor_recovery_codes',
		'two_factor_secret',
	];

	/**
	 * The attributes that should be cast.
	 *
	 * @var array
	 */
	protected $casts = [
		'email_verified_at' => 'datetime',
	];

	/**
	 * The accessors to append to the model's array form.
	 *
	 * @var array
	 */
	protected $appends = [
		'profile_photo_url',
	];

	public function apiServices() {
		return $this->hasMany(InstitutionApiService::class);
	}

	public static function userInstitution() {
		return DB::table('users as u')
			->join('institutions as i','i.id','=','u.institution_id')
			->select('u.id','u.name','institution','approved','u.email','privs')
			->orderByDesc('u.created_at')
			->whereNull('deleted_at')
			->get();

	}

	public static function userLibrary()
	{
		return DB::table('users as u')
			->join('libraries as l','l.id','=','u.library_id')
			->select('u.id','u.name','l.library_name','approved','u.email','privs')
			->orderByDesc('u.created_at')
			->whereNull('deleted_at')
			->get();

	}


}
