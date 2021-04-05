<?php 

namespace App\Middleware;

use Core\Middleware;
use Web\Url;
use Web\Response;
use Session\Session;

class IsProfMiddleware extends Middleware {
	
	public function handle(): bool {
		if(Session::readUser("connected") != null && Session::readUser("type") == "professeur") return true;
		return false;
	}
	
}
