<?php
namespace app\controller;
use core\main\Controller;
use core\lib\Template;
class Main extends Controller {

    /**
     * @perm(guest)
     */
    public function index() {
    	
    	Template::Show('main');
	}

}