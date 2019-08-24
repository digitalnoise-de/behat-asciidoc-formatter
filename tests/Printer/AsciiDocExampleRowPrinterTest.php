<?php
declare(strict_types=1);

namespace Digitalnoise\Behat\AsciiDocFormatter\Tests\Printer;

use Behat\Gherkin\Node\ExampleNode;
use Behat\Gherkin\Node\ExampleTableNode;
use Behat\Gherkin\Node\OutlineNode;
use Behat\Testwork\Output\Formatter;
use Digitalnoise\Behat\AsciiDocFormatter\Printer\AsciiDocExampleRowPrinter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @author Philip Weinke <philip.weinke@digitalnoise.de>
 */
class AsciiDocExampleRowPrinterTest extends TestCase
{
    /**
     * @var InMemoryOutputPrinter
     */
    private $outputPrinter;

    /**
     * @var MockObject|Formatter
     */
    private $formatter;

    /**
     * @var AsciiDocExampleRowPrinter
     */
    private $printer;

    public function test()
    {
        $examples = new ExampleTableNode([], 'Examples');
        $outline  = new OutlineNode('My Outline', [], [], $examples, 'Scenario Outline', 1);
        $example  = new ExampleNode('', [], [], ['Username' => 'Peter', 'E-Mail' => 'peter@peterson.test'], 1);

        $this->printer->printExampleRow($this->formatter, $outline, $example, []);

        self::assertEquals("| Peter | peter@peterson.test\n", $this->outputPrinter->getOutput());
    }

    protected function setUp()
    {
        parent::setUp();

        $this->outputPrinter = new InMemoryOutputPrinter();

        $this->formatter = $this->createMock(Formatter::class);
        $this->formatter->method('getOutputPrinter')->willReturn($this->outputPrinter);

        $this->printer = new AsciiDocExampleRowPrinter();
    }
}
