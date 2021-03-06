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
     * @param array        $values Array of initial value "overrides"
     */
    public function __construct ($parentContext = null, $values = [])
    {
        $this->parentContext = $parentContext;
        $this->setDefaults();
        $this->set($values);
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
     * @param array $values
     * @return static Array of initial value "overrides"
     */
    public function createSubContext ($values = [])
    {
        return new static($this, $values);
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

    /**
     * Helper function that allows to define multiple value "overrides" at once
     * @param array $values
     * @return $this
     */
    public function set ($values = [])
    {
        foreach ($values as $key => $value)
            $this->{$key} = $value;
        return $this;
    }

    public function __get ($name)
    {
        // First, check if the "actual" property exists
        if (property_exists($this, $name))
            return $this->{$name};
        // Try to retrieve the property from the "values" array
        else if (array_key_exists($name, $this->values))
            return $this->values[$name];
        // Try to retrieve the property from the parent context
        else if ($this->parentContext !== null && ($parentValue = $this->parentContext->{$name}) !== null)
            return $parentValue;
        // Try to retrieve the property from the "defaults" array
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
    private function setDefaults ()
    {
        $class = get_class($this);
        do {
            $reflectionClass   = new \ReflectionClass($class);
            $defaultProperties = $reflectionClass->getDefaultProperties();
            foreach ($defaultProperties as $name => $value) {
                if (
                    preg_match('/^_(.*)$/', $name, $matches) &&
                    !$this->isDefaultSet($matches[1])
                )
                    $this->setDefault($matches[1], $value);
            }
        } while ($class = get_parent_class($class));
    }
}
