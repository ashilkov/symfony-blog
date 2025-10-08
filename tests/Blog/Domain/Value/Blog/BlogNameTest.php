<?php

namespace App\Tests\Blog\Domain\Value\Blog;

use App\Blog\Domain\Value\Blog\BlogName;
use PHPUnit\Framework\TestCase;

class BlogNameTest extends TestCase
{
    public function testValidBlogName(): void
    {
        $name = new BlogName('Test Blog');
        $this->assertEquals('Test Blog', $name->value());
        $this->assertEquals('Test Blog', (string) $name);
    }

    public function testTrimsInput(): void
    {
        $name = new BlogName('  Test Blog  ');
        $this->assertEquals('Test Blog', $name->value());
    }

    public function testEmptyBlogNameThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Blog name cannot be empty.');
        new BlogName('');
    }

    public function testWhitespaceOnlyThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Blog name cannot be empty.');
        new BlogName('   ');
    }

    public function testTooLongBlogNameThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Blog name is too long (max 200 chars).');
        new BlogName(str_repeat('a', 201));
    }

    public function testEquals(): void
    {
        $name1 = new BlogName('Test Blog');
        $name2 = new BlogName('Test Blog');
        $name3 = new BlogName('Different Blog');

        $this->assertTrue($name1->equals($name2));
        $this->assertFalse($name1->equals($name3));
    }

    public function testMaxLengthIsAllowed(): void
    {
        $name = new BlogName(str_repeat('a', 200));
        $this->assertEquals(200, mb_strlen($name->value()));
    }
}
