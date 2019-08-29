<?php
declare(strict_types=1);

namespace Digitalnoise\Behat\AsciiDocFormatter\Tests\Printer;

use Behat\Testwork\Output\Formatter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @author Philip Weinke <philip.weinke@digitalnoise.de>
 */
abstract class PrinterTestCase extends TestCase
{
    /**
     * @var MockObject|Formatter
     */
    protected $formatter;

    /**
     * @var FakeAsciiDocOutputPrinter
     */
    private $outputPrinter;

    /**
     * @param string $expected
     */
    protected function assertOutput(string $expected): void
    {
        self::assertEquals($expected, $this->outputPrinter->getOutput());
    }

    /**
     * @param string $expected
     */
    protected function assertFilename(string $expected): void
    {
        self::assertEquals($expected, $this->outputPrinter->getCurrentFilename());
    }

    protected function setUp()
    {
        parent::setUp();

        $this->outputPrinter = new FakeAsciiDocOutputPrinter();

        $this->formatter = $this->createMock(Formatter::class);
        $this->formatter->method('getOutputPrinter')->willReturn($this->outputPrinter);
    }
}
