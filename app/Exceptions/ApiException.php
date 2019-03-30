<?php
namespace App\Exceptions;


class ApiException extends \Exception
{

    function _construct($msg='')
    {
        parent::_construct($msg);
    }
}
