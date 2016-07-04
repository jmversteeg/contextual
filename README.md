contextual
==========
 > Simple object-oriented contexts

[![Build Status][travis-image]][travis-url]
[![Code Quality][scrutinizer-g-image]][scrutinizer-g-url]
[![Code Coverage][coveralls-image]][coveralls-url]
[![Packagist Version][packagist-image]][packagist-url]

## Usage

```php

/**
 * @property boolean $JSON
 * @property boolean $admin
 * @property string  $type
 */
class ResponseContext extends \jmversteeg\contextual\Context
{

    // Declare default values with a preceding underscore
    
    private $_JSON  = false;
    private $_admin = false;
    private $_type  = 'body';

}

$responseContext = new ResponseContext([
    'JSON' => true,
    'type' => 'ajax'
]);

$responseContext->JSON;
// => true
$responseContext->admin;
// => false

$subContext = $responseContext->createSubContext([
    'admin' => true
]);

$subContext->JSON;
// => true
$subContext->admin;
// => true

```
 
[travis-image]: https://img.shields.io/travis/jmversteeg/contextual.svg?style=flat-square
[travis-url]: https://travis-ci.org/jmversteeg/contextual

[scrutinizer-g-image]: https://img.shields.io/scrutinizer/g/jmversteeg/contextual.svg?style=flat-square
[scrutinizer-g-url]: https://scrutinizer-ci.com/g/jmversteeg/contextual/

[coveralls-image]: https://img.shields.io/coveralls/jmversteeg/contextual.svg?style=flat-square
[coveralls-url]: https://coveralls.io/r/jmversteeg/contextual

[packagist-image]: https://img.shields.io/packagist/v/jmversteeg/contextual.svg?style=flat-square
[packagist-url]: https://packagist.org/packages/jmversteeg/contextual

## License

MIT © JM Versteeg
