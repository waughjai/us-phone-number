<?php

declare( strict_types = 1 );
namespace WaughJ\USPhoneNumber;

class USPhoneNumber
{
    public function __construct( $number )
    {
        if ( is_string( $number ) )
        {
            $number = self::divideNumberStringIntoParts( $number );
        }

        if ( gettype( $number ) === 'object' && get_class( $number ) === self::class )
        {
            $this->country_code = $number->country_code;
            $this->area_code    = $number->area_code;
            $this->prefix       = $number->prefix;
            $this->line_number  = $number->line_number;
        }
        else if ( is_array( $number ) )
        {
            $number_of_parts = count( $number );
            if ( $number_of_parts < 3 )
            {
                throw new InvalidPhoneNumberException( $number, "Not enough parts were filled out. You need at least 3 parts: the area code, prefix, & line number. You provided only {$number_of_parts} parts." );
            }
            else if ( $number_of_parts < 4 )
            {
                array_unshift( $number, self::DEFAULT_COUNTRY_CODE );
            }

            $this->country_code = $number[ 0 ];
            $this->area_code    = $number[ 1 ];
            $this->prefix       = $number[ 2 ];
            $this->line_number  = $number[ 3 ];

            // Test.
            $country_code_length = strlen( $this->country_code );
            $area_code_length = strlen( $this->area_code );
            $prefix_length = strlen( $this->prefix );
            $line_number_length = strlen( $this->line_number );
    
            if ( $country_code_length !== 1 )
            {
                throw new InvalidPhoneNumberException( implode( '', $number ), "Country code doesn’t have the right number of digits. It needs to be 1 digit; you gave {$country_code_length} digits." );
            }
            else if ( $area_code_length !== 3 )
            {
                throw new InvalidPhoneNumberException( implode( '', $number ), "Area code doesn’t have the right number of digits. It needs to be 3 digits; you gave {$area_code_length} digits." );
            }
            else if ( $prefix_length !== 3 )
            {
                throw new InvalidPhoneNumberException( implode( '', $number ), "Prefix doesn’t have the right number of digits. It needs to be 3 digits; you gave {$prefix_length} digits." );
            }
            else if ( $line_number_length !== 4 )
            {
                throw new InvalidPhoneNumberException( implode( '', $number ), "Line number doesn’t have the right number of digits. It needs to be 4 digits; you gave {$line_number_length} digits." );
            }
        }
        else
        {
            $type = gettype( $number );
            if ( $type === 'object' )
            {
                $type = get_class( $number );
            }
            $number_string = "";
            try
            {
                $number_string = ( string )( $number );
            }
            catch ( \Exception $e )
            {
                // this is fine. Do nothing.
            }
            throw new InvalidPhoneNumberException( $number_string, "Invalid type given for input. Expects USPhoneNumber, string, or array; received {$type}" );
        }
    }

    public function getRaw() : string
    {
        return $this->country_code . $this->area_code . $this->prefix . $this->line_number;
    }

    public function getFormat( string $format ) : string
    {
        return str_ireplace
        (
            'c',
            $this->country_code,
            str_ireplace
            (
                'a',
                $this->area_code,
                str_ireplace
                (
                    'p',
                    $this->prefix,
                    str_ireplace
                    (
                        'l',
                        $this->line_number,
                        $format
                    )
                )
            )
        );
    }

    private static function divideNumberStringIntoParts( string $number ) : array
    {
        $has_country_code = strlen( self::removeAllNonNumericCharacters( $number ) ) > 10;
        $parts = [];
        $part_count = 0;
        $current_part_length = 0;
        $state = "Start";

        if ( !$has_country_code )
        {
            $parts[] = self::DEFAULT_COUNTRY_CODE;
            $part_count++;
        }

        $characters = preg_split( "//u", $number, -1, PREG_SPLIT_NO_EMPTY );
        foreach ( $characters as $character )
        {
            if ( preg_match( "/([\s\-_–\)\]]+)/u", $character ) )
            {
                $state = "New Part";
            }
            else if ( preg_match( "/[0-9]/u", $character ) )
            {
                if ( $state === "New Part" || $state === "Start" || $current_part_length === self::getMaxLengthForPart( $part_count ) )
                {
                    $part_count++;
                    if ( $part_count > 4 )
                    {
                        break;
                    }
                    $parts[] = "";
                    $state = "Normal";
                    $current_part_length = 0;
                }
                $parts[ $part_count - 1 ] .= $character;
                ++$current_part_length;
            }
        }
        return $parts;
    }

    private static function removeAllNonNumericCharacters( string $string ) : string
    {
        return preg_replace( "/[^0-9]/", "", $string );
    }

    private static function getMaxLengthForPart( int $part_count ) : int
    {
        return self::PART_MAX_LENGTHS[ $part_count - 1 ];
    }

    private $country_code = null;
    private $area_code = null;
    private $prefix = null;
	private $line_number = null;
	
    private const DEFAULT_COUNTRY_CODE = '1';
    private const PART_MAX_LENGTHS =
    [
        1,
        3,
        3,
        4
    ];
}
