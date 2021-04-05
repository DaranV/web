<?php 

namespace App\Middleware;

use Core\Middleware;
use Web\Url;
use Web\Response;
use Session\Session;

class NoAuthMiddleware extends Middleware {
	
	public function handle(): bool {
		if(Session::readUser("token") == null) return true;
		
		Url::redirect("/", false, 301);
		return false;
	}
	
}
