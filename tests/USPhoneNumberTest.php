<?php

declare( strict_types = 1 );

use PHPUnit\Framework\TestCase;
use WaughJ\USPhoneNumber\USPhoneNumber;
use WaughJ\USPhoneNumber\InvalidPhoneNumberException;

class USPhoneNumberTest extends TestCase
{
	public function testBasicPhone() : void
	{
		$tel = new USPhoneNumber( '18005552028' );
		$this->assertEquals( '18005552028', $tel->getRaw() );
		$this->assertEquals( '1-800-555-2028', $tel->getFormat( 'c-a-p-l' ) );
		$this->assertEquals( '800-555-2028', $tel->getFormat( 'a-p-l' ) );
	}

	public function testPhoneWithoutCountryCode() : void
	{
		$tel = new USPhoneNumber( '2068219054' );
		$this->assertEquals( '12068219054', $tel->getRaw() );
		$this->assertEquals( '(206) 821-9054', $tel->getFormat( '(a) p-l' ) );
	}

	public function testInvalidPhone() : void
	{
		$this->expectException( InvalidPhoneNumberException::class );
		$tel = new USPhoneNumber( '8218219' );
	}

	public function testAlreadyFormattedPhone() : void
	{
		$tel = new USPhoneNumber( '253-772-1988' );
		$this->assertEquals( '12537721988', $tel->getRaw() );
		$this->assertEquals( '( 253 ) 772 – 1988', $tel->getFormat( '( a ) p – l' ) );
	}

	public function testAlreadyFormattedPhone2() : void
	{
		$tel = new USPhoneNumber( '(253) 772-1988' );
		$this->assertEquals( '12537721988', $tel->getRaw() );
		$this->assertEquals( '( 253 ) 772 – 1988', $tel->getFormat( '( a ) p – l' ) );
	}

	public function testAlreadyFormattedPhone3() : void
	{
		$tel = new USPhoneNumber( '[253]7721988' );
		$this->assertEquals( '12537721988', $tel->getRaw() );
		$this->assertEquals( '( 253 ) 772 – 1988', $tel->getFormat( '( a ) p – l' ) );
	}

	public function testMalformattedPhone() : void
	{
		$this->expectException( InvalidPhoneNumberException::class );
		$tel = new USPhoneNumber( '25-772-1988' );
	}

	public function testMalformattedPhone2() : void
	{
		$this->expectException( InvalidPhoneNumberException::class );
		$tel = new USPhoneNumber( '253-7721-1988' );
	}

	public function testMalformattedPhone3() : void
	{
		$this->expectException( InvalidPhoneNumberException::class );
		$tel = new USPhoneNumber( '253-772-198' );
	}

	public function testMalformattedPhone4() : void
	{
		$this->expectException( InvalidPhoneNumberException::class );
		$tel = new USPhoneNumber( '11-253-772-1988' );
	}

	public function testUSPhoneNumberAsInput() : void
	{
		$tel = new USPhoneNumber( '18005552028' );
		$tel2 = new USPhoneNumber( $tel );
		$this->assertEquals( '18005552028', $tel2->getRaw() );
		$this->assertEquals( '1-800-555-2028', $tel2->getFormat( 'c-a-p-l' ) );
		$this->assertEquals( '800-555-2028', $tel2->getFormat( 'a-p-l' ) );
	}
}
