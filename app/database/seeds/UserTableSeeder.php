<?php

class UserTableSeeder extends Seeder
{

	public function run()
	{
		DB::table('users')->delete();
		App\Models\User::create(array(
			'email'    => 'chawkinsuf@gmail.com',
			'password' => '123456',
			'nest_password' => null
		));
	}

}
