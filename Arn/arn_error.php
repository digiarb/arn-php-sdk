<?php

class Arn_Error extends Exception
{
    private $errorsDetails = array();
    public function __construct($message=null, $errors=array())
    {
        parent::__construct($message);
        if(!is_array($errors))
            $errors = array($errors);
        $this->errorsDetails = $errors;
    }
    
    public function getErrorsDetails()
    {
        return $this->errorsDetails;
    }
}
?>