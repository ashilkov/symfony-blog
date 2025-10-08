<?php

declare(strict_types=1);

namespace App\Tests\Blog\Domain\Value\Subscription;

use App\Blog\Domain\Value\Subscription\SubscriptionId;
use PHPUnit\Framework\TestCase;

class SubscriptionIdTest extends TestCase
{
    public function testValue(): void
    {
        $subscriptionId = new SubscriptionId(123);
        $this->assertEquals(123, $subscriptionId->value());
    }
    
    public function testEquals(): void
    {
        $subscriptionId1 = new SubscriptionId(123);
        $subscriptionId2 = new SubscriptionId(123);
        $subscriptionId3 = new SubscriptionId(456);
        
        $this->assertTrue($subscriptionId1->equals($subscriptionId2));
        $this->assertFalse($subscriptionId1->equals($subscriptionId3));
    }
}
