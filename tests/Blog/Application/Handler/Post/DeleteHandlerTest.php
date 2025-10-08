<?php

namespace App\Tests\Blog\Application\Handler\Post;

use App\Blog\Application\Command\Post\DeleteCommand;
use App\Blog\Application\Handler\Post\DeleteHandler;
use App\Blog\Domain\Model\Post;
use App\Blog\Domain\Repository\PostRepositoryInterface;
use PHPUnit\Framework\TestCase;

class DeleteHandlerTest extends TestCase
{
    private $posts;
    private $handler;

    protected function setUp(): void
    {
        $this->posts = $this->createMock(PostRepositoryInterface::class);
        $this->handler = new DeleteHandler($this->posts);
    }

    public function testDeletePostSuccessfully(): void
    {
        $postId = 123;
        $command = new DeleteCommand($postId);

        $post = $this->createMock(Post::class);
        
        $this->posts->expects($this->once())
            ->method('findOneBy')
            ->with(['id' => $postId])
            ->willReturn($post);

        $this->posts->expects($this->once())
            ->method('remove')
            ->with($post, true);

        ($this->handler)($command);
    }

    public function testDeleteNonExistentPostThrowsException(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Post not found.');

        $postId = 123;
        $command = new DeleteCommand($postId);

        $this->posts->expects($this->once())
            ->method('findOneBy')
            ->with(['id' => $postId])
            ->willReturn(null);

        ($this->handler)($command);
    }
}
