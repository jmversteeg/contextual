<?php

namespace test\jmversteeg\contextual;

use test\jmversteeg\contextual\helper\TypedContext;
use test\jmversteeg\contextual\helper\SubTypedContext;

class ContextTest extends \PHPUnit_Framework_TestCase
{

    public function testDefaults ()
    {
        $typedContext = new TypedContext();
        $this->assertEquals('foo', $typedContext->type);
        $this->assertEquals(false, $typedContext->beepboop);
    }

    public function testSubClassDefaults ()
    {
        $subTypedContext = new SubTypedContext();
        $this->assertEquals('foo', $subTypedContext->type);
        $this->assertEquals(true, $subTypedContext->beepboop);
    }

    public function testValue ()
    {
        $typedContext       = new TypedContext();
        $typedContext->type = 'bar';
        $this->assertEquals('bar', $typedContext->type);
    }

    public function testUnknownValue ()
    {
        $typedContext = new TypedContext();
        $this->assertEquals(null, $typedContext->foobarbaz);
    }

    public function testCreateSubContext ()
    {
        $typedContext = new TypedContext();
        $subContext   = $typedContext->createSubContext();
        $this->assertTrue($subContext instanceof TypedContext);
    }

    public function testValueInheritance ()
    {
        $typedContext       = new TypedContext();
        $subContext         = $typedContext->createSubContext();
        $typedContext->type = 'bar';
        $this->assertEquals('bar', $subContext->type);
    }

    public function testValueOverride ()
    {
        $typedContext       = new TypedContext();
        $subContext         = $typedContext->createSubContext();
        $subContext->type   = 'foobaz';
        $typedContext->type = 'bar';
        $this->assertEquals('foobaz', $subContext->type);
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

        $subContextB->type = 'bar';
        $subContextC->type = 'baz';

        $this->assertEquals(['foo'], $typedContext->getValueTree('type'));
        $this->assertEquals(['foo'], $subContextA->getValueTree('type'));
        $this->assertEquals(['foo', 'foo'], $subContextA->getValueTree('type', false));

        $this->assertEquals(['foo', 'bar', 'baz'], $subContextC->getValueTree('type'));
        $this->assertEquals(['foo', 'foo', 'bar', 'baz'], $subContextC->getValueTree('type', false));
    }
}
