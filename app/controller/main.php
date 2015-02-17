<?php
namespace app\controller;
use core\main\Controller;
use core\lib\Template;
use core\lib\GET;

class Main extends Controller {

    /**
     * @perm(guest)
     * @method(get)
     */
    public function index() {
        $get = new GET();
        $get->setRequired(true)->get('test');
    	Template::Show('main');
	}

}