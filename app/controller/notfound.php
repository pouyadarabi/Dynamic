<?php
namespace app\controller;
use core\main\Controller;
use core\lib\Template;

class NotFound extends Controller {

	public function index() {
	    http_response_code(404);
	    Template::set('URL', $_SERVER['REQUEST_URI']);
	    Template::Show('notfound');
	}
}
