<?php

class UserTableSeeder extends Seeder
{

	public function run()
	{
		DB::table('users')->delete();
		User::create(array(
			'email'    => 'chawkinsuf@gmail.com',
			'password' => '123456',
			'nest_password' => '51gBJAh*yOEN*SK'
		));
	}

}