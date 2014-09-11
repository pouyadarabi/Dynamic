<?php
namespace app\controller;
use core\main\Controller;
class Main extends Controller {

    /**
     * @perm(guest)
     */
    public function index() {
    	echo 'Hello World!'; 
	}

}