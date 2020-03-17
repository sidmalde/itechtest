<?php

namespace itechTest\Components\Social\Twitter\Exceptions;


/**
 * Class TwitterManagerMissingCredentialException
 *
 * @package itechTest\Components\Social\Twitter\Exceptions
 */
class TwitterManagerMissingCredentialException extends \LogicException
{
    protected $message = 'You Are Missing Or More Credentials For The TwitterManager To Work';
}