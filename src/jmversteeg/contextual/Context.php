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
    public function __construct ($parentContext = null)
    {
        $this->parentContext = $parentContext;

        $this->importDefaults();
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
     * Checks whether a default value is set for the given key
     *
     * @param string $key
     *
     * @return bool
     */
    protected function isDefaultSet ($key)
    {
        return isset($this->defaults[$key]);
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
            if ($value !== null && (!$removeDuplicates || (!count($values) || $values[count($values) - 1] !== $value)))
                $values[] = $value;
        }
        return $values;
    }

    public function __get ($name)
    {
        if (property_exists($this, $name))
            return $this->{$name};
        else if (array_key_exists($name, $this->values))
            return $this->values[$name];
        else if ($this->parentContext !== null && ($parentValue = $this->parentContext->{$name}) !== null)
            return $parentValue;
        else
            return array_key_exists($name, $this->defaults) ? $this->defaults[$name] : null;
    }

    public function __set ($name, $value)
    {
        if (property_exists($this, $name))
            $this->{$name} = $value;
        else
            $this->values[$name] = $value;
    }

    /**
     * Loads all the default properties starting with an underscore into the defaults array
     */
    private function importDefaults ()
    {
        $class = get_class($this);
        do {
            $reflectionClass   = new \ReflectionClass($class);
            $defaultProperties = $reflectionClass->getDefaultProperties();
            foreach ($reflectionClass->getProperties() as $property)
                if (
                    !$property->isStatic() &&
                    $property->isDefault() &&
                    preg_match('/^_(.*)$/', $property->name, $matches) &&
                    !$this->isDefaultSet($matches[1])
                )
                    $this->setDefault($matches[1], $defaultProperties[$property->name]);
        } while ($class = get_parent_class($class));
    }
}