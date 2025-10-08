<?php

declare(strict_types=1);

namespace App\Tests\Blog\Domain\Value\Blog;

use App\Blog\Domain\Value\Blog\BlogId;
use PHPUnit\Framework\TestCase;

class BlogIdTest extends TestCase
{
    public function testValue(): void
    {
        $blogId = new BlogId(123);
        $this->assertEquals(123, $blogId->value());
    }
    
    public function testEquals(): void
    {
        $blogId1 = new BlogId(123);
        $blogId2 = new BlogId(123);
        $blogId3 = new BlogId(456);
        
        $this->assertTrue($blogId1->equals($blogId2));
        $this->assertFalse($blogId1->equals($blogId3));
    }
}
