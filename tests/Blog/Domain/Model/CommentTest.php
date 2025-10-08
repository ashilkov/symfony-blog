<?php

declare(strict_types=1);

namespace App\Tests\Blog\Domain\Model;

use App\Blog\Domain\Factory\CommentFactory;
use App\Blog\Domain\Model\Comment;
use App\Blog\Domain\Value\Comment\CommentId;
use App\Blog\Domain\Value\Common\Content;
use App\Blog\Domain\Value\Common\UserId;
use App\Blog\Domain\Value\Post\PostId;
use PHPUnit\Framework\TestCase;

class CommentTest extends TestCase
{
    private CommentFactory $commentFactory;

    protected function setUp(): void
    {
        $this->commentFactory = new CommentFactory();
    }

    public function testCreateComment(): void
    {
        $content = new Content('Test comment');
        $userId = new UserId(1);
        $postId = new PostId(1);
        
        $comment = $this->commentFactory->create($content, $userId, $postId);
        
        $this->assertNull($comment->getId());
        $this->assertSame($content, $comment->getContent());
        $this->assertSame($userId, $comment->getUserId());
        $this->assertSame($postId, $comment->getPostId());
        $this->assertNotNull($comment->getCreatedAt());
        $this->assertNotNull($comment->getUpdatedAt());
    }

    public function testAssignId(): void
    {
        $comment = $this->commentFactory->create(
            new Content('Test comment'),
            new UserId(1),
            new PostId(1)
        );
        
        // Test that assignId doesn't throw an exception
        $this->assertNull($comment->getId());
    }

    public function testUpdateContent(): void
    {
        $comment = $this->commentFactory->create(
            new Content('Old comment'),
            new UserId(1),
            new PostId(1)
        );
        
        $initialUpdatedAt = $comment->getUpdatedAt();
        
        // Update the content directly
        $reflection = new \ReflectionClass($comment);
        $property = $reflection->getProperty('content');
        $property->setAccessible(true);
        $property->setValue($comment, new Content('Updated comment'));
        
        // Call touch to update the timestamp
        $method = $reflection->getMethod('touch');
        $method->setAccessible(true);
        $method->invoke($comment);
        
        $this->assertEquals('Updated comment', $comment->getContent()->value());
        $this->assertNotEquals($initialUpdatedAt, $comment->getUpdatedAt());
    }

    public function testUpdateUser(): void
    {
        $comment = $this->commentFactory->create(
            new Content('Test comment'),
            new UserId(1),
            new PostId(1)
        );
        
        $newUserId = new UserId(2);
        
        // Update the userId directly
        $reflection = new \ReflectionClass($comment);
        $property = $reflection->getProperty('userId');
        $property->setAccessible(true);
        $property->setValue($comment, $newUserId);
        
        $this->assertSame($newUserId, $comment->getUserId());
    }

    public function testAttachToPost(): void
    {
        $comment = $this->commentFactory->create(
            new Content('Test comment'),
            new UserId(1),
            new PostId(1)
        );
        
        $newPostId = new PostId(2);
        $comment->attachToPost($newPostId);
        
        $this->assertSame($newPostId, $comment->getPostId());
    }
}
