<?php

namespace test\jmversteeg\contextual\helper;

use jmversteeg\contextual\Context;

class TypedContext extends Context
{

    private $_type = 'foo';

    /**
     * @return string
     */
    public function getType ()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType ($type)
    {
        $this->type = $type;
    }
}