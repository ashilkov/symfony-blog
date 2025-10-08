<?php

namespace App\Tests\Blog\Domain\Value\Post;

use App\Blog\Domain\Value\Post\PostTitle;
use PHPUnit\Framework\TestCase;

class PostTitleTest extends TestCase
{
    public function testValidPostTitle(): void
    {
        $title = new PostTitle('Test Post Title');
        $this->assertEquals('Test Post Title', $title->value());
        $this->assertEquals('Test Post Title', (string) $title);
    }

    public function testTrimsInput(): void
    {
        $title = new PostTitle('  Test Post Title  ');
        $this->assertEquals('Test Post Title', $title->value());
    }

    public function testEmptyPostTitleThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Post title cannot be empty.');
        new PostTitle('');
    }

    public function testWhitespaceOnlyThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Post title cannot be empty.');
        new PostTitle('   ');
    }

    public function testTooLongPostTitleThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Post title is too long (max 200 chars).');
        new PostTitle(str_repeat('a', 201));
    }

    public function testEquals(): void
    {
        $title1 = new PostTitle('Test Title');
        $title2 = new PostTitle('Test Title');
        $title3 = new PostTitle('Different Title');

        $this->assertTrue($title1->equals($title2));
        $this->assertFalse($title1->equals($title3));
    }

    public function testMaxLengthIsAllowed(): void
    {
        $title = new PostTitle(str_repeat('a', 200));
        $this->assertEquals(200, mb_strlen($title->value()));
    }
}
