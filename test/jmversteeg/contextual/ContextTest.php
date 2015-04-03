<?php

namespace test\jmversteeg\contextual;

use test\jmversteeg\contextual\helper\TypedContext;

class ContextTest extends \PHPUnit_Framework_TestCase
{

    public function testDefaults ()
    {
        $typedContext = new TypedContext();
        $this->assertEquals('foo', $typedContext->getType());
    }

    public function testValue ()
    {
        $typedContext = new TypedContext();
        $typedContext->setType('bar');
        $this->assertEquals('bar', $typedContext->getType());
    }

    public function testCreateSubContext ()
    {
        $typedContext = new TypedContext();
        $subContext   = $typedContext->createSubContext();
        $this->assertTrue($subContext instanceof TypedContext);
    }

    public function testValueInheritance ()
    {
        $typedContext = new TypedContext();
        $subContext   = $typedContext->createSubContext();
        $typedContext->setType('bar');
        $this->assertEquals('bar', $subContext->getType());
    }

    public function testValueOverride ()
    {
        $typedContext = new TypedContext();
        $subContext   = $typedContext->createSubContext();
        $subContext->setType('foobaz');
        $typedContext->setType('bar');
        $this->assertEquals('foobaz', $subContext->getType());
    }

    public function testGetContextTree ()
    {
        $typedContext = new TypedContext();
        $subContext   = $typedContext->createSubContext();
        $this->assertEquals([$typedContext], $typedContext->getContextTree());
        $this->assertEquals([$typedContext, $subContext], $subContext->getContextTree());
    }

    public function testGetValueTree ()
    {
        $typedContext = new TypedContext();
        $subContextA  = $typedContext->createSubContext();
        $subContextB  = $subContextA->createSubContext();
        $subContextC  = $subContextB->createSubContext();

        $subContextB->setType('bar');
        $subContextC->setType('baz');

        $this->assertEquals(['foo'], $typedContext->getValueTree('type'));
        $this->assertEquals(['foo'], $subContextA->getValueTree('type'));
        $this->assertEquals(['foo', 'foo'], $subContextA->getValueTree('type', false));

        $this->assertEquals(['foo', 'bar', 'baz'], $subContextC->getValueTree('type'));
        $this->assertEquals(['foo', 'foo', 'bar', 'baz'], $subContextC->getValueTree('type', false));
    }
}