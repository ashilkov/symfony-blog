<?php

namespace App\Tests\Blog\Domain\Value\Common;

use App\Blog\Domain\Value\Common\Content;
use PHPUnit\Framework\TestCase;

class ContentTest extends TestCase
{
    public function testValidContent(): void
    {
        $content = new Content('Test content');
        $this->assertEquals('Test content', $content->value());
        $this->assertEquals('Test content', (string) $content);
    }

    public function testNormalizesNewlines(): void
    {
        $content = new Content("Line1\r\nLine2\rLine3\nLine4");
        $this->assertEquals("Line1\nLine2\nLine3\nLine4", $content->value());
    }

    public function testCollapsesSpaces(): void
    {
        $content = new Content("Test    content   with   spaces");
        $this->assertEquals('Test content with spaces', $content->value());
    }

    public function testTrimsInput(): void
    {
        $content = new Content('  Test content  ');
        $this->assertEquals('Test content', $content->value());
    }

    public function testEmptyContentThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Content cannot be empty.');
        new Content('');
    }

    public function testWhitespaceOnlyThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Content cannot be empty.');
        new Content("   \n   ");
    }

    public function testTooLongContentThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Content is too long (max 10,000 chars).');
        new Content(str_repeat('a', 10001));
    }

    public function testEquals(): void
    {
        $content1 = new Content('Test content');
        $content2 = new Content('Test content');
        $content3 = new Content('Different content');

        $this->assertTrue($content1->equals($content2));
        $this->assertFalse($content1->equals($content3));
    }

    public function testMaxLengthIsAllowed(): void
    {
        $content = new Content(str_repeat('a', 10000));
        $this->assertEquals(10000, mb_strlen($content->value()));
    }

    public function testPreservesNewlinesButCollapsesOtherSpaces(): void
    {
        $content = new Content("Line1    \n    Line2");
        $this->assertEquals("Line1\nLine2", $content->value());
    }
}
