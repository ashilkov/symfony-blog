<?php

declare(strict_types=1);

namespace App\Tests\Blog\Domain\Value\Common;

use App\Blog\Domain\Value\Common\UserId;
use PHPUnit\Framework\TestCase;

class UserIdTest extends TestCase
{
    public function testValue(): void
    {
        $userId = new UserId(123);
        $this->assertEquals(123, $userId->value());
    }
    
    public function testEquals(): void
    {
        $userId1 = new UserId(123);
        $userId2 = new UserId(123);
        $userId3 = new UserId(456);
        
        $this->assertTrue($userId1->equals($userId2));
        $this->assertFalse($userId1->equals($userId3));
    }
}
