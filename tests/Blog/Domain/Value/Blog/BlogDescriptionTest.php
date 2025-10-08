<?php

namespace App\Tests\Blog\Domain\Value\Blog;

use App\Blog\Domain\Value\Blog\BlogDescription;
use PHPUnit\Framework\TestCase;

class BlogDescriptionTest extends TestCase
{
    public function testValidBlogDescription(): void
    {
        $description = new BlogDescription('Test Description');
        $this->assertEquals('Test Description', $description->value());
        $this->assertEquals('Test Description', (string) $description);
    }

    public function testNormalizesWhitespace(): void
    {
        $description = new BlogDescription("Test   \n   Description");
        $this->assertEquals('Test Description', $description->value());
    }

    public function testTrimsInput(): void
    {
        $description = new BlogDescription('  Test Description  ');
        $this->assertEquals('Test Description', $description->value());
    }

    public function testEmptyBlogDescriptionThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Blog description cannot be empty.');
        new BlogDescription('');
    }

    public function testWhitespaceOnlyThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Blog description cannot be empty.');
        new BlogDescription("   \n   ");
    }

    public function testTooLongBlogDescriptionThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Blog description is too long (max 2000 chars).');
        new BlogDescription(str_repeat('a', 2001));
    }

    public function testEquals(): void
    {
        $desc1 = new BlogDescription('Test Description');
        $desc2 = new BlogDescription('Test Description');
        $desc3 = new BlogDescription('Different Description');

        $this->assertTrue($desc1->equals($desc2));
        $this->assertFalse($desc1->equals($desc3));
    }

    public function testMaxLengthIsAllowed(): void
    {
        $description = new BlogDescription(str_repeat('a', 2000));
        $this->assertEquals(2000, mb_strlen($description->value()));
    }
}
