<?php
namespace Radisand\ApiGeneralSchemeMyGenetics\Exceptions;

use Exception;

class AuthServiceClientNotProvideTokenException extends Exception
{
    public int $codeException = 401;
}