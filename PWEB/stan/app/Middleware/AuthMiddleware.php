<?php 

namespace App\Middleware;

use Core\Middleware;
use Web\Url;
use Web\Response;
use Session\Session;
use App\Model\Online;

class AuthMiddleware extends Middleware {
	
	public function handle(): bool {
		if(Session::readUser("connected") != null) {
			$online = new Online();
			$online->refresh();
			
			if(Session::readUser("type") != "professeur")
				$online->update(Session::readUser("id_etu"));
		
			return true;
		}
		
		
		Url::redirect(Url::connect("auth"), true, 301);
		return false;
	}
	
}
