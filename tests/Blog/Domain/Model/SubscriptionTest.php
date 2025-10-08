<?php

declare(strict_types=1);

namespace App\Tests\Blog\Domain\Model;

use App\Blog\Domain\Model\Subscription;
use App\Blog\Domain\Value\Blog\BlogId;
use App\Blog\Domain\Value\Common\UserId;
use App\Blog\Domain\Value\Subscription\SubscriptionId;
use PHPUnit\Framework\TestCase;

class SubscriptionTest extends TestCase
{
    public function testCreateSubscription(): void
    {
        $subscription = new Subscription();
        
        $this->assertNull($subscription->getId());
        $this->assertNull($subscription->getBlogId());
        $this->assertNull($subscription->getSubscriberId());
    }

    public function testAssignId(): void
    {
        $subscription = new Subscription();
        $subscriptionId = new SubscriptionId(1);
        $subscription->assignId($subscriptionId);
        
        $this->assertSame($subscriptionId, $subscription->getId());
    }

    public function testAttachToBlog(): void
    {
        $subscription = new Subscription();
        $blogId = new BlogId(1);
        $subscription->attachToBlog($blogId);
        
        $this->assertSame($blogId, $subscription->getBlogId());
    }

    public function testAssignSubscriber(): void
    {
        $subscription = new Subscription();
        $subscriberId = new UserId(1);
        $subscription->assignSubscriber($subscriberId);
        
        $this->assertSame($subscriberId, $subscription->getSubscriberId());
    }

    public function testTimestamps(): void
    {
        $subscription = new Subscription();
        
        // Timestamps should be set immediately upon creation
        $this->assertNotNull($subscription->getCreatedAt());
        $this->assertNotNull($subscription->getUpdatedAt());
        
        // Store initial timestamps
        $initialCreatedAt = $subscription->getCreatedAt();
        $initialUpdatedAt = $subscription->getUpdatedAt();
        
        // After touch, updatedAt should change
        $reflection = new \ReflectionClass($subscription);
        $method = $reflection->getMethod('touch');
        $method->setAccessible(true);
        $method->invoke($subscription);
        
        $this->assertSame($initialCreatedAt, $subscription->getCreatedAt());
        $this->assertNotEquals($initialUpdatedAt, $subscription->getUpdatedAt());
    }
}
