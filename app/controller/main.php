<?php
namespace app\controller;
use core\main\Controller;
use core\lib\Template;

class Main extends Controller {

    /**
     * @perm(guest)
     * @method(get)
     */
    public function index() {
    	Template::Show('main');
	}

}