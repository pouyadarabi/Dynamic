<?php
namespace core\lib;

class Request {

    protected $Clean = true;

    protected $Required = true;

    protected $TypeArray = array();

    protected $Type = false;

    protected $MyCounter = 0;

    protected $Range = false;

    protected $Cases = false;

    protected $Length = [0,100];

    protected function getvar($index, $ReqType, $Required, $Clean, $Length, $Range, $Cases, $default) {
        if ($this->Type === false)
            $type = (isset($this->TypeArray[$this->MyCounter]) ? $this->TypeArray[$this->MyCounter ++] : 's');
        else
            $type = $this->Type;
        
        $Req = self::GetArrayByType($ReqType);
        $Check = $this->Check($index, $ReqType, $type);
        
        $input_data = $default;
        
        if ($Check) {
            $input = $Req[$index];
            
            if ($Length !== false) {
                if ((is_string($input) && (strlen($input) > $Length[1] || strlen($input) < $Length[0])))
                    Helper::printLast(Messages::get('checkinput'));
            }
            if ($Range !== false) {
                if ($input < $Range[0] || $input > $Range[1])
                    Helper::printLast(Messages::get('checkinput'));
            }
            if ($Cases !== false) {
                if (! in_array($input, $Cases))
                    Helper::printLast(Messages::get('checkinput'));
            }
            if ($Clean !== false) {
                $input_data = Security::CleanXssString($input);
            } else
                $input_data = $input;
        } else {
            if ($Required)
                Helper::printLast(Messages::get('checkinput'));
        }
        $this->Range = false;
        $this->Cases = false;
        $this->Length = false;
        return $input_data;
    }

    private function GetArrayByType($Type) {
        switch ($Type) {
            case 'p':
                return $_POST;
            case 'g':
                return $_GET;
        }
    }

    private function Check($index, $ReqType, $VarType) {
        return Security::CheckInput($index, $ReqType, $VarType);
    }

    public function CheckisSeted($type = 'p') {
        $array = $this->GetArrayByType($type);
        if (count($array) > 0)
            return true;
        else
            return false;
    }

    /**
     *
     * @param int $start            
     * @param int $end            
     */
    public function SetRange($start, $end) {
        $this->Range = [$start,$end];
        return $this;
    }

    public function SetWhiteList(array $array) {
        $this->Cases = $array;
        return $this;
    }

    /**
     *
     * @param boolean $Clean            
     */
    public function setClean($Clean) {
        $this->Clean = $Clean;
        return $this;
    }

    /**
     *
     * @param boolean $Required            
     */
    public function setRequired($Required) {
        $this->Required = $Required;
        return $this;
    }

    /**
     *
     * @param mixed: $Type            
     */
    public function setType($Type) {
        if (is_array($Type)) {
            $this->TypeArray = $Type;
        } else {
            $this->Type = $Type;
        }
        return $this;
    }

    /**
     *
     * @param number $MinLength            
     * @param number $MaxLength            
     */
    public function setLength($MinLength, $MaxLength) {
        $this->Length = [$MinLength,$MaxLength];
        return $this;
    }
}