<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace RlcCrm;

/**
 * Description of ObjectModel
 *
 * @author pavel
 */
class ObjectModel
{

    public function __construct($data = array())
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    public function validate()
    {
        return array();
    }

}
