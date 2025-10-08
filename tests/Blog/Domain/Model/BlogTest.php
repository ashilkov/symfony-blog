<?php

declare(strict_types=1);

namespace App\Tests\Blog\Domain\Model;

use App\Blog\Domain\Factory\BlogFactory;
use App\Blog\Domain\Model\Blog;
use App\Blog\Domain\Value\Blog\BlogDescription;
use App\Blog\Domain\Value\Blog\BlogId;
use App\Blog\Domain\Value\Blog\BlogName;
use App\Blog\Domain\Value\Common\UserId;
use PHPUnit\Framework\TestCase;

class BlogTest extends TestCase
{
    private BlogFactory $blogFactory;

    protected function setUp(): void
    {
        $this->blogFactory = new BlogFactory();
    }

    public function testCreateBlog(): void
    {
        $name = new BlogName('Test Blog');
        $description = new BlogDescription('Test Description');
        
        $blog = $this->blogFactory->create($name, $description);
        
        $this->assertNull($blog->getId());
        $this->assertSame($name, $blog->getName());
        $this->assertSame($description, $blog->getDescription());
        $this->assertNotNull($blog->getCreatedAt());
        $this->assertNotNull($blog->getUpdatedAt());
    }

    public function testAssignId(): void
    {
        $blog = $this->blogFactory->create(
            new BlogName('Test Blog'),
            new BlogDescription('Test Description')
        );
        
        $blogId = new BlogId(1);
        $blog->assignId($blogId);
        
        $this->assertSame($blogId, $blog->getId());
    }

    public function testSetDescription(): void
    {
        $blog = $this->blogFactory->create(
            new BlogName('Test Blog'),
            new BlogDescription('Old Description')
        );
        
        $newDescription = new BlogDescription('New Description');
        $blog->setDescription($newDescription);
        
        $this->assertSame($newDescription, $blog->getDescription());
        $this->assertNotEquals($blog->getCreatedAt(), $blog->getUpdatedAt());
    }

    public function testGetBlogUsersInitiallyEmpty(): void
    {
        $blog = $this->blogFactory->create(
            new BlogName('Test Blog'),
            new BlogDescription('Test Description')
        );
        
        $this->assertEmpty(iterator_to_array($blog->getBlogUsers()));
    }

    public function testGetPostsInitiallyEmpty(): void
    {
        $blog = $this->blogFactory->create(
            new BlogName('Test Blog'),
            new BlogDescription('Test Description')
        );
        
        $this->assertEmpty(iterator_to_array($blog->getPosts()));
    }
}
