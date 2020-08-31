<?php

namespace Tests\Unit;

use App\Traits\AddsModelNameTrait;
use PHPUnit\Framework\TestCase;

class FooBarQux
{
    use AddsModelNameTrait;
}

class AddsModelNameTraitTest extends TestCase
{
    /**
     * Check that the model class name gets returned on calling the `getModelNameAttribute()`
     * method which can be accessed on real model by the attribute `modelName`.
     *
     * @return void
     */
    public function testGetModelNameAttribute()
    {
        $object = new FooBarQux();
        $this->assertEquals('FooBarQux', $object->getModelNameAttribute());
    }
}
