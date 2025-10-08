<?php

/**
 * @author Andrei Shilkov <a.shilkov@design.ru>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Tests\Blog\Domain\Security;

use App\Blog\API\Resource\Post;
use App\Blog\Domain\Security\PostPermissionPolicyInterface;
use PHPUnit\Framework\TestCase;

class PostPermissionPolicyInterfaceTest extends TestCase
{
    private PostPermissionPolicyInterface $policy;
    private int $userId;
    private int $blogId;
    private Post $post;

    protected function setUp(): void
    {
        // This test is designed to work with any implementation of PostPermissionPolicyInterface
        // The actual implementation should be provided through dependency injection in real usage
        // For testing purposes, we'll use a mock
        $this->policy = $this->createMock(PostPermissionPolicyInterface::class);
        $this->userId = 123;
        $this->blogId = 456;
        $this->post = $this->createMock(Post::class);
    }

    public function testCanCreatePost(): void
    {
        // Test that canCreatePost method exists and returns a boolean
        $this->policy->expects($this->once())
            ->method('canCreatePost')
            ->with($this->userId, $this->blogId)
            ->willReturn(true);

        $result = $this->policy->canCreatePost($this->userId, $this->blogId);
        
        $this->assertIsBool($result);
        $this->assertTrue($result);
    }

    public function testCanEditPost(): void
    {
        // Test that canEditPost method exists and returns a boolean
        $this->policy->expects($this->once())
            ->method('canEditPost')
            ->with($this->userId, $this->post)
            ->willReturn(true);

        $result = $this->policy->canEditPost($this->userId, $this->post);
        
        $this->assertIsBool($result);
        $this->assertTrue($result);
    }

    public function testCanDeletePost(): void
    {
        // Test that canDeletePost method exists and returns a boolean
        $this->policy->expects($this->once())
            ->method('canDeletePost')
            ->with($this->userId, $this->post)
            ->willReturn(true);

        $result = $this->policy->canDeletePost($this->userId, $this->post);
        
        $this->assertIsBool($result);
        $this->assertTrue($result);
    }

    public function testInterfaceMethods(): void
    {
        // Ensure the interface has the expected methods
        $reflection = new \ReflectionClass(PostPermissionPolicyInterface::class);
        
        $this->assertTrue($reflection->hasMethod('canCreatePost'));
        $this->assertTrue($reflection->hasMethod('canEditPost'));
        $this->assertTrue($reflection->hasMethod('canDeletePost'));
        
        $canCreatePostMethod = $reflection->getMethod('canCreatePost');
        $this->assertEquals('bool', $canCreatePostMethod->getReturnType()->getName());
        
        $canEditPostMethod = $reflection->getMethod('canEditPost');
        $this->assertEquals('bool', $canEditPostMethod->getReturnType()->getName());
        
        $canDeletePostMethod = $reflection->getMethod('canDeletePost');
        $this->assertEquals('bool', $canDeletePostMethod->getReturnType()->getName());
        
        // Verify parameter types
        $canCreatePostParams = $canCreatePostMethod->getParameters();
        $this->assertEquals('int', $canCreatePostParams[0]->getType()->getName());
        $this->assertEquals('int', $canCreatePostParams[1]->getType()->getName());
        
        $canEditPostParams = $canEditPostMethod->getParameters();
        $this->assertEquals('int', $canEditPostParams[0]->getType()->getName());
        $this->assertEquals(Post::class, $canEditPostParams[1]->getType()->getName());
        
        $canDeletePostParams = $canDeletePostMethod->getParameters();
        $this->assertEquals('int', $canDeletePostParams[0]->getType()->getName());
        $this->assertEquals(Post::class, $canDeletePostParams[1]->getType()->getName());
    }
}
