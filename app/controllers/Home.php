<?php
namespace App\Controllers;
use View;

class Home extends Base {
	protected $layout = "layouts.main";

	public function getIndex(){
		$this->layout->content = View::make('home');
	}
}