<?php

namespace App\Tests\Blog\Application\Handler\Post;

use App\Blog\Application\Command\Post\UpdateCommand;
use App\Blog\Application\Handler\Post\UpdateHandler;
use App\Blog\Domain\Model\Post;
use App\Blog\Domain\Repository\PostRepositoryInterface;
use App\Blog\Domain\Value\Common\Content;
use App\Blog\Domain\Value\Post\PostTitle;
use PHPUnit\Framework\TestCase;

class UpdateHandlerTest extends TestCase
{
    private $posts;
    private $handler;

    protected function setUp(): void
    {
        $this->posts = $this->createMock(PostRepositoryInterface::class);
        $this->handler = new UpdateHandler($this->posts);
    }

    public function testUpdatePostSuccessfully(): void
    {
        $postId = 123;
        $command = new UpdateCommand(
            id: $postId,
            title: 'Updated Title',
            content: 'Updated Content'
        );

        $post = $this->createMock(Post::class);
        
        $this->posts->expects($this->once())
            ->method('findOneBy')
            ->with(['id' => $postId])
            ->willReturn($post);

        $post->expects($this->once())
            ->method('rename')
            ->with($this->isInstanceOf(PostTitle::class));
            
        $post->expects($this->once())
            ->method('changeContent')
            ->with($this->isInstanceOf(Content::class));

        $this->posts->expects($this->once())
            ->method('save')
            ->with($post, true);

        $result = ($this->handler)($command);

        $this->assertSame($post, $result);
    }

    public function testUpdatePostWithOnlyTitle(): void
    {
        $postId = 123;
        $command = new UpdateCommand(
            id: $postId,
            title: 'Updated Title',
            content: null
        );

        $post = $this->createMock(Post::class);
        
        $this->posts->expects($this->once())
            ->method('findOneBy')
            ->with(['id' => $postId])
            ->willReturn($post);

        $post->expects($this->once())
            ->method('rename')
            ->with($this->isInstanceOf(PostTitle::class));
            
        $post->expects($this->never())
            ->method('changeContent');

        $this->posts->expects($this->once())
            ->method('save')
            ->with($post, true);

        $result = ($this->handler)($command);

        $this->assertSame($post, $result);
    }

    public function testUpdatePostWithOnlyContent(): void
    {
        $postId = 123;
        $command = new UpdateCommand(
            id: $postId,
            title: null,
            content: 'Updated Content'
        );

        $post = $this->createMock(Post::class);
        
        $this->posts->expects($this->once())
            ->method('findOneBy')
            ->with(['id' => $postId])
            ->willReturn($post);

        $post->expects($this->never())
            ->method('rename');
            
        $post->expects($this->once())
            ->method('changeContent')
            ->with($this->isInstanceOf(Content::class));

        $this->posts->expects($this->once())
            ->method('save')
            ->with($post, true);

        $result = ($this->handler)($command);

        $this->assertSame($post, $result);
    }

    public function testUpdateNonExistentPostThrowsException(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Blog not found.');

        $postId = 123;
        $command = new UpdateCommand(
            id: $postId,
            title: 'Updated Title',
            content: 'Updated Content'
        );

        $this->posts->expects($this->once())
            ->method('findOneBy')
            ->with(['id' => $postId])
            ->willReturn(null);

        ($this->handler)($command);
    }
}
