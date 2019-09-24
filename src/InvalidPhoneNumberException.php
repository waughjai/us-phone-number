<?php

declare( strict_types = 1 );
namespace WaughJ\USPhoneNumber;

class InvalidPhoneNumberException extends \Exception
{
    public function __construct( string $number, string $message )
    {
        parent::__construct( "US Phone Number “{$number}” isn’t a valid number: {$message}" );
    }
}
