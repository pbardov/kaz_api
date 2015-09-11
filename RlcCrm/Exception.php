<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace RlcCrm;

/**
 * Description of Exception
 *
 * @author pavel
 */
class Exception extends \Exception
{
    protected $_error = null;
    
    public function __construct($message = null, $error = null)
    {
        if (empty($message))
            $message = get_called_class();
        $this->_error = $error;
        if (is_scalar($this->_error))
            $message .= ": $error";
        parent::__construct($message, 0, null);
    }
    
    public function getError()
    {
        return $this->_error;
    }
}
