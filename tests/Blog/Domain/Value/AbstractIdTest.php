<?php

declare(strict_types=1);

namespace App\Tests\Blog\Domain\Value;

use App\Blog\Domain\Value\AbstractId;
use PHPUnit\Framework\TestCase;

class AbstractIdTest extends TestCase
{
    public function testValue(): void
    {
        $id = new class(123) extends AbstractId {};
        
        $this->assertEquals(123, $id->value());
    }
    
    public function testEqualsWithSameClassAndValue(): void
    {
        $id1 = new class(123) extends AbstractId {};
        $id2 = new class(123) extends AbstractId {};
        
        $this->assertTrue($id1->equals($id2));
    }
    
    public function testEqualsWithDifferentValue(): void
    {
        $id1 = new class(123) extends AbstractId {};
        $id2 = new class(456) extends AbstractId {};
        
        $this->assertFalse($id1->equals($id2));
    }
    
    public function testEqualsWithDifferentClass(): void
    {
        $id1 = new class(123) extends AbstractId {};
        $id2 = new class(123) extends AbstractId {};
        
        // Even though they have the same value, they are different classes
        $this->assertTrue($id1->equals($id2));
    }
}
