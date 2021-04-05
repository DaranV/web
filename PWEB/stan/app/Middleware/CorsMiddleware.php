<?php 
namespace App\Middleware;

use Core\Middleware;
use Web\Response;

class CorsMiddleware extends Middleware {
	
	public function handle(): bool {
		Response::addHeaders(["Content-Type: application/json", "Access-Control-Allow-Origin: *", "Access-Control-Allow-Headers: token"]);
		return true;
	}
	
}
