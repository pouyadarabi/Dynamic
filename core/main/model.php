<?php
namespace core\main;
use core\lib\Security;

class Model extends AbstractClass
{

    protected $db;
    protected $Sec;

    public function __construct ()
    {
        //$this->db = new Packages_db();
        $this->Sec = new Security();
    }
}
