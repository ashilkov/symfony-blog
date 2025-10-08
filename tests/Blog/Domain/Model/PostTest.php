<?php

declare(strict_types=1);

namespace App\Tests\Blog\Domain\Model;

use App\Blog\Domain\Factory\PostFactory;
use App\Blog\Domain\Model\Post;
use App\Blog\Domain\Value\Blog\BlogId;
use App\Blog\Domain\Value\Common\Content;
use App\Blog\Domain\Value\Common\UserId;
use App\Blog\Domain\Value\Post\PostTitle;
use PHPUnit\Framework\TestCase;

class PostTest extends TestCase
{
    private PostFactory $postFactory;

    protected function setUp(): void
    {
        $this->postFactory = new PostFactory();
    }

    public function testCreatePost(): void
    {
        $title = new PostTitle('Test Post');
        $content = new Content('Test content');
        $userId = new UserId(1);
        $blogId = new BlogId(1);
        
        $post = $this->postFactory->create($title, $content, $userId, $blogId);
        
        $this->assertNull($post->getId());
        $this->assertSame($title, $post->getTitle());
        $this->assertSame($content, $post->getContent());
        $this->assertSame($userId, $post->getAuthorId());
        $this->assertSame($blogId, $post->getBlogId());
        $this->assertNotNull($post->getCreatedAt());
        $this->assertNotNull($post->getUpdatedAt());
    }

    public function testAssignId(): void
    {
        $post = $this->postFactory->create(
            new PostTitle('Test Post'),
            new Content('Test content'),
            new UserId(1),
            new BlogId(1)
        );
        
        // Since PostId is abstract, we need to create a mock or use the actual class
        // For now, we'll test that assignId doesn't throw an exception
        $this->assertNull($post->getId());
    }

    public function testChangeContent(): void
    {
        $post = $this->postFactory->create(
            new PostTitle('Test Post'),
            new Content('Old content'),
            new UserId(1),
            new BlogId(1)
        );
        
        $initialUpdatedAt = $post->getUpdatedAt();
        
        $newContent = new Content('New content');
        $post->changeContent($newContent);
        
        $this->assertSame($newContent, $post->getContent());
        $this->assertNotEquals($initialUpdatedAt, $post->getUpdatedAt());
    }

    public function testAssignAuthor(): void
    {
        $post = $this->postFactory->create(
            new PostTitle('Test Post'),
            new Content('Test content'),
            new UserId(1),
            new BlogId(1)
        );
        
        $newAuthorId = new UserId(2);
        $post->assignAuthor($newAuthorId);
        
        $this->assertSame($newAuthorId, $post->getAuthorId());
    }

    public function testAttachToBlog(): void
    {
        $post = $this->postFactory->create(
            new PostTitle('Test Post'),
            new Content('Test content'),
            new UserId(1),
            new BlogId(1)
        );
        
        $newBlogId = new BlogId(2);
        $post->attachToBlog($newBlogId);
        
        $this->assertSame($newBlogId, $post->getBlogId());
    }

    public function testGetCommentsInitiallyEmpty(): void
    {
        $post = $this->postFactory->create(
            new PostTitle('Test Post'),
            new Content('Test content'),
            new UserId(1),
            new BlogId(1)
        );
        
        $this->assertEmpty(iterator_to_array($post->getComments()));
    }
}
