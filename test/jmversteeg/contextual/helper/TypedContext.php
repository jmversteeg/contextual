<?php

namespace test\jmversteeg\contextual\helper;

use jmversteeg\contextual\Context;

/**
 * @property string  $type     Type
 * @property boolean $beepboop Something beepy is coming this way
 */
class TypedContext extends Context
{

    private $_type     = 'foo';
    private $_beepboop = false;

}
