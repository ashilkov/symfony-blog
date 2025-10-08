<?php

declare(strict_types=1);

namespace App\Tests\Blog\Domain\Value\Post;

use App\Blog\Domain\Value\Post\PostId;
use PHPUnit\Framework\TestCase;

class PostIdTest extends TestCase
{
    public function testValue(): void
    {
        $postId = new PostId(123);
        $this->assertEquals(123, $postId->value());
    }
    
    public function testEquals(): void
    {
        $postId1 = new PostId(123);
        $postId2 = new PostId(123);
        $postId3 = new PostId(456);
        
        $this->assertTrue($postId1->equals($postId2));
        $this->assertFalse($postId1->equals($postId3));
    }
}
