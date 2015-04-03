<?php

namespace jmversteeg\contextual;

class Context
{

    /**
     * The parent context, if available.
     * @var Context|null
     */
    private $parentContext = null;

    /**
     * The default values for this context. Values in this array will be used when the value is not defined, and no
     * parent context is provided or the parent context does not define the value either.
     * @var mixed[]
     */
    private $defaults = array();

    /**
     * An array of values. These take precedence over both the value inherited from the parent as well as the default
     * values.
     * @var mixed[]
     */
    private $values = array();

    /**
     * Constructs a new context
     *
     * @param Context|null $parentContext
     */
    function __construct ($parentContext = null)
    {
        $this->parentContext = $parentContext;

        $reflectionClass   = new \ReflectionClass(get_class($this));
        $defaultProperties = $reflectionClass->getDefaultProperties();
        foreach ($reflectionClass->getProperties() as $property)
            if (!$property->isStatic() && $property->isDefault() && preg_match('/^_(.*)$/', $property->getName(), $matches))
                $this->defaults[$matches[1]] = $defaultProperties[$property->name];
    }

    /**
     * Sets the default value for a key. The value will be used when no parent context is provided or the parent
     * context does not define the value.
     *
     * @param string $key
     * @param mixed  $value
     */
    protected function setDefault ($key, $value)
    {
        $this->defaults[$key] = $value;
    }

    /**
     * Creates a subcontext of the same type as the current context.
     * @return static
     */
    public function createSubContext ()
    {
        return new static($this);
    }

    /**
     * Returns an array of parent contexts up to and including the current context, in order of ascending depth
     * @return Context[]
     */
    public function getContextTree ()
    {
        $parentContext = $this;
        $parents       = array($parentContext);
        while (($parentContext = $parentContext->parentContext) !== null)
            array_unshift($parents, $parentContext);
        return $parents;
    }

    /**
     * Based on the context tree, returns an array of values from this context and its parents, in order of ascending
     * depth
     *
     * @param string $name             The name of the value
     * @param bool   $removeDuplicates Whether to remove consecutive duplicate values
     *
     * @return mixed[]
     */
    public function getValueTree ($name, $removeDuplicates = true)
    {
        $contextTree = $this->getContextTree();
        $values      = array();
        foreach ($contextTree as $context) {
            $value = $context->{$name};
            if ($value === null)
                continue;
            if (!$removeDuplicates || (!count($values) || $values[count($values) - 1] !== $value))
                $values[] = $value;
        }
        return $values;
    }

    function __get ($name)
    {
        if (array_key_exists($name, $this->values))
            return $this->values[$name];
        else if ($this->parentContext !== null && ($parentValue = $this->parentContext->{$name}) !== null)
            return $parentValue;
        else if (array_key_exists($name, $this->defaults))
            return $this->defaults[$name];
        else
            return null;
    }

    function __set ($name, $value)
    {
        $this->values[$name] = $value;
    }
}