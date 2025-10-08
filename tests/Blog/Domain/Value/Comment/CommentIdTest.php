<?php

declare(strict_types=1);

namespace App\Tests\Blog\Domain\Value\Comment;

use App\Blog\Domain\Value\Comment\CommentId;
use PHPUnit\Framework\TestCase;

class CommentIdTest extends TestCase
{
    public function testValue(): void
    {
        $commentId = new CommentId(123);
        $this->assertEquals(123, $commentId->value());
    }
    
    public function testEquals(): void
    {
        $commentId1 = new CommentId(123);
        $commentId2 = new CommentId(123);
        $commentId3 = new CommentId(456);
        
        $this->assertTrue($commentId1->equals($commentId2));
        $this->assertFalse($commentId1->equals($commentId3));
    }
}
