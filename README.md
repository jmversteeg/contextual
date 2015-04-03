contextual
==========
**Elegant object-oriented context pattern implementation**

[![Build Status][travis-image]][travis-url]
[![Code Quality][scrutinizer-g-image]][scrutinizer-g-url]
[![Code Coverage][coveralls-image]][coveralls-url]
[![Packagist Version][packagist-image]][packagist-url]

#### example usage

```php

class ResponseContext extends \jmversteeg\contextual\Context
{

    // Declare default values with a preceding underscore
    private $_JSON  = false;
    private $_admin = false;

    /**
     * @return boolean
     */
    public function isJSON ()
    {
        return $this->JSON;
    }

    /**
     * @param boolean $JSON
     */
    public function setJSON ($JSON)
    {
        $this->JSON = $JSON;
    }

    /**
     * @return boolean
     */
    public function isAdmin ()
    {
        return $this->admin;
    }

    /**
     * @param boolean $admin
     */
    public function setAdmin ($admin)
    {
        $this->admin = $admin;
    }
}

$responseContext = new ResponseContext();
$responseContext->setJSON(true);

$responseContext->isJSON();
// => true
$responseContext->isAdmin();
// => false

$subContext = $responseContext->createSubContext();
$subContext->setAdmin(true);

$subContext->isJSON();
// => true
$subContext->isAdmin();
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