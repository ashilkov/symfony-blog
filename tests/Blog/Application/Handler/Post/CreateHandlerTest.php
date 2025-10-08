<?php

namespace App\Tests\Blog\Application\Handler\Post;

use App\Blog\Application\Command\Post\CreateCommand;
use App\Blog\Application\Handler\Post\CreateHandler;
use App\Blog\Domain\Factory\PostFactory;
use App\Blog\Domain\Model\Blog;
use App\Blog\Domain\Model\Post;
use App\Blog\Domain\Repository\BlogRepositoryInterface;
use App\Blog\Domain\Repository\PostRepositoryInterface;
use App\Blog\Domain\User\UserReadModelPortInterface;
use App\Blog\Domain\User\UserSummary;
use App\Blog\Domain\Value\Blog\BlogId;
use App\Blog\Domain\Value\Common\Content;
use App\Blog\Domain\Value\Common\UserId;
use App\Blog\Domain\Value\Post\PostTitle;
use PHPUnit\Framework\TestCase;

class CreateHandlerTest extends TestCase
{
    private $posts;
    private $userReadModel;
    private $blogs;
    private $postFactory;
    private $handler;

    protected function setUp(): void
    {
        $this->posts = $this->createMock(PostRepositoryInterface::class);
        $this->userReadModel = $this->createMock(UserReadModelPortInterface::class);
        $this->blogs = $this->createMock(BlogRepositoryInterface::class);
        $this->postFactory = $this->createMock(PostFactory::class);

        $this->handler = new CreateHandler(
            $this->posts,
            $this->userReadModel,
            $this->blogs,
            $this->postFactory
        );
    }

    public function testCreatePostSuccessfully(): void
    {
        $userId = 123;
        $blogId = 456;
        $command = new CreateCommand(
            title: 'Test Post',
            content: 'Test Content',
            blogId: $blogId,
            userId: $userId
        );

        $userSummary = $this->createMock(UserSummary::class);
        $blog = $this->createMock(Blog::class);
        $post = $this->createMock(Post::class);

        $this->userReadModel->expects($this->once())
            ->method('findSummaryById')
            ->with($userId)
            ->willReturn($userSummary);

        $this->blogs->expects($this->once())
            ->method('findOneBy')
            ->with(['id' => $blogId])
            ->willReturn($blog);

        $this->postFactory->expects($this->once())
            ->method('create')
            ->with(
                $this->isInstanceOf(PostTitle::class),
                $this->isInstanceOf(Content::class),
                $this->isInstanceOf(UserId::class),
                $this->isInstanceOf(BlogId::class)
            )
            ->willReturn($post);

        $this->posts->expects($this->once())
            ->method('save')
            ->with($post, true);

        $result = ($this->handler)($command);

        $this->assertSame($post, $result);
    }

    public function testCreatePostWithoutUserThrowsException(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('User is required to create a post.');

        $command = new CreateCommand(
            title: 'Test Post',
            content: 'Test Content',
            blogId: 456,
            userId: null
        );

        ($this->handler)($command);
    }

    public function testCreatePostWithNonExistentUserThrowsException(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('User is required to create a post.');

        $userId = 123;
        $command = new CreateCommand(
            title: 'Test Post',
            content: 'Test Content',
            blogId: 456,
            userId: $userId
        );

        $this->userReadModel->expects($this->once())
            ->method('findSummaryById')
            ->with($userId)
            ->willReturn(null);

        ($this->handler)($command);
    }

    public function testCreatePostWithNonExistentBlogThrowsException(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Blog is required to create a post.');

        $userId = 123;
        $blogId = 456;
        $command = new CreateCommand(
            title: 'Test Post',
            content: 'Test Content',
            blogId: $blogId,
            userId: $userId
        );

        $userSummary = $this->createMock(UserSummary::class);
        $this->userReadModel->expects($this->once())
            ->method('findSummaryById')
            ->with($userId)
            ->willReturn($userSummary);

        $this->blogs->expects($this->once())
            ->method('findOneBy')
            ->with(['id' => $blogId])
            ->willReturn(null);

        ($this->handler)($command);
    }
}
