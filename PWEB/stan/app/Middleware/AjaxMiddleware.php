<?php 
namespace App\Middleware;

use Core\Middleware;
use Web\Response;

class AjaxMiddleware extends Middleware {
	
	public function handle(): bool {
		Response::addHeaders(["Content-Type: application/json", "Access-Control-Allow-Origin: *"]);
		return true;
	}
	
}
