US Phone Number
=========================

Class for easily reformatting a US phone number.

Constructor takes in 1 argument, which can either be a string with at least 10 numbers, an array with at least 3 items, or another instance of USPhoneNumber class. Any inputs that violate these rules or which lead to parts of a phone number with the wrong number of digits will throw an InvalidPhoneNumberException.

## Example

    use WaughJ\USPhoneNumber\USPhoneNumber;

    $phone_number = new USPhoneNumber( '2565552028' );
    echo $phone_number->getFormat( '(a) p – l' );

Will print:

    (256) 555 — 2028

## Changelog

### 0.1.0
* Initial Release