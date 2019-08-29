<?php
declare(strict_types=1);

namespace Digitalnoise\Behat\AsciiDocFormatter\Tests\Printer;

use Behat\Gherkin\Node\ExampleNode;
use Behat\Gherkin\Node\ExampleTableNode;
use Behat\Gherkin\Node\OutlineNode;
use Digitalnoise\Behat\AsciiDocFormatter\Printer\AsciiDocExampleRowPrinter;

/**
 * @author Philip Weinke <philip.weinke@digitalnoise.de>
 */
class AsciiDocExampleRowPrinterTest extends PrinterTestCase
{
    /**
     * @var AsciiDocExampleRowPrinter
     */
    private $printer;

    public function test_print_example_row_print_table_row()
    {
        $examples = new ExampleTableNode([], 'Examples');
        $outline  = new OutlineNode('My Outline', [], [], $examples, 'Scenario Outline', 1);
        $example  = new ExampleNode('', [], [], ['Username' => 'Peter', 'E-Mail' => 'peter@peterson.test'], 1);

        $this->printer->printExampleRow($this->formatter, $outline, $example, []);

        $this->assertOutput("| Peter | peter@peterson.test\n");
    }

    protected function setUp()
    {
        parent::setUp();

        $this->printer = new AsciiDocExampleRowPrinter();
    }
}
