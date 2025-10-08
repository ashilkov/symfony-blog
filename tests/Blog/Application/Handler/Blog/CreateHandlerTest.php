<?php

namespace App\Tests\Blog\Application\Handler\Blog;

use App\Blog\Application\Command\Blog\CreateCommand;
use App\Blog\Application\Handler\Blog\CreateHandler;
use App\Blog\Domain\Factory\BlogFactory;
use App\Blog\Domain\Model\Blog;
use App\Blog\Domain\Repository\BlogRepositoryInterface;
use App\Blog\Domain\User\UserReadModelPortInterface;
use App\Blog\Domain\User\UserSummary;
use App\Blog\Domain\Value\Blog\BlogDescription;
use App\Blog\Domain\Value\Blog\BlogName;
use App\Blog\Domain\Value\Common\UserId;
use PHPUnit\Framework\TestCase;

class CreateHandlerTest extends TestCase
{
    private $blogs;
    private $userReadModel;
    private $blogFactory;
    private $handler;

    protected function setUp(): void
    {
        $this->blogs = $this->createMock(BlogRepositoryInterface::class);
        $this->userReadModel = $this->createMock(UserReadModelPortInterface::class);
        $this->blogFactory = $this->createMock(BlogFactory::class);
        $eventDispatcher = $this->createMock(\Psr\EventDispatcher\EventDispatcherInterface::class);

        $this->handler = new CreateHandler(
            $this->blogs,
            $this->userReadModel,
            $eventDispatcher,
            $this->blogFactory
        );
    }

    public function testCreateBlogSuccessfully(): void
    {
        $userId = 123;
        $command = new CreateCommand(
            name: 'Test Blog',
            description: 'Test Description',
            userId: $userId
        );

        $userSummary = $this->createMock(UserSummary::class);
        $blog = $this->createMock(Blog::class);

        $this->userReadModel->expects($this->once())
            ->method('findSummaryById')
            ->with($userId)
            ->willReturn($userSummary);

        $this->blogFactory->expects($this->once())
            ->method('create')
            ->with(
                $this->isInstanceOf(BlogName::class),
                $this->isInstanceOf(BlogDescription::class),
            )
            ->willReturn($blog);

        $this->blogs->expects($this->once())
            ->method('save')
            ->with($blog, true);

        $result = ($this->handler)($command);

        $this->assertSame($blog, $result);
    }

    public function testCreateBlogWithoutUserThrowsException(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('User is required to create a blog.');

        $command = new CreateCommand(
            name: 'Test Blog',
            description: 'Test Description',
            userId: null
        );

        ($this->handler)($command);
    }

    public function testCreateBlogWithNonExistentUserThrowsException(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('User is required to create a blog.');

        $userId = 123;
        $command = new CreateCommand(
            name: 'Test Blog',
            description: 'Test Description',
            userId: $userId
        );

        $this->userReadModel->expects($this->once())
            ->method('findSummaryById')
            ->with($userId)
            ->willReturn(null);

        ($this->handler)($command);
    }
}
