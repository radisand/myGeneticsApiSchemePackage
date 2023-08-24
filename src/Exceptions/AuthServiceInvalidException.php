<?php
namespace Radisand\ApiGeneralSchemeMyGenetics\Exceptions;

use Exception;

class AuthServiceInvalidException extends Exception
{
    public int $codeException = 401;
}