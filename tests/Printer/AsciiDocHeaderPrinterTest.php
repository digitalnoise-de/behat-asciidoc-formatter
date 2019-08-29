<?php
declare(strict_types=1);

namespace Digitalnoise\Behat\AsciiDocFormatter\Tests\Printer;

use Behat\Testwork\Output\Formatter;
use Digitalnoise\Behat\AsciiDocFormatter\Printer\AsciiDocHeaderPrinter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class AsciiDocHeaderPrinterTest extends TestCase
{
    /**
     * @var FakeAsciiDocOutputPrinter
     */
    private $outputPrinter;

    /**
     * @var MockObject|Formatter
     */
    private $formatter;

    public function test_print_header_should_print_title_and_toc()
    {
        $printer = new AsciiDocHeaderPrinter('index.adoc', 'Document Title');

        $printer->printHeader($this->formatter);

        self::assertEquals('index.adoc', $this->outputPrinter->getCurrentFilename());
        self::assertEquals(
            "= Document Title\n" .
            ":doctype: book\n" .
            ":icons: font\n" .
            ":toc:\n" .
            ":toclevels: 3\n\n",
            $this->outputPrinter->getOutput()
        );
    }

    protected function setUp()
    {
        parent::setUp();

        $this->outputPrinter = new FakeAsciiDocOutputPrinter();

        $this->formatter = $this->createMock(Formatter::class);
        $this->formatter->method('getOutputPrinter')->willReturn($this->outputPrinter);
    }
}
