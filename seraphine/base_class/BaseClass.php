<?php

namespace BaseClass;

use rec\ReceiveData;

class BaseClass
{
    public array $rec;
    function __construct()
    {
        $this->rec = ReceiveData::rec();
    }
}