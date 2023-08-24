<?php
namespace Radisand\ApiGeneralSchemeMyGenetics\Exceptions;

use Exception;

class AuthServiceNotProvidedTokenException extends Exception
{
    public int $codeException = 401;
}