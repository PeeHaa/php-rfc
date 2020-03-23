<?php declare(strict_types=1);

namespace AsyncBot\Plugin\PhpRfcsTest\Unit\Exception;

use AsyncBot\Plugin\PhpRfcs\Exception\UnexpectedHtmlFormat;
use PHPUnit\Framework\TestCase;

final class UnexpectedHtmlFormatTest extends TestCase
{
    public function testConstructorFormatsMessageCorrectly(): void
    {
        $this->expectException(UnexpectedHtmlFormat::class);
        $this->expectExceptionMessage('Could not find the "ELEMENT" element in the row');

        throw new UnexpectedHtmlFormat('ELEMENT');
    }
}
