<?php
declare(strict_types=1);

namespace Digitalnoise\Behat\AsciiDocFormatter\Tests\Printer;

use Behat\Gherkin\Node\ExampleTableNode;
use Behat\Gherkin\Node\FeatureNode;
use Behat\Gherkin\Node\OutlineNode;
use Behat\Gherkin\Node\StepNode;
use Behat\Testwork\Tester\Result\TestResult;
use Digitalnoise\Behat\AsciiDocFormatter\Printer\AsciiDocOutlineTablePrinter;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @author Philip Weinke <philip.weinke@digitalnoise.de>
 */
class AsciiDocOutlineTablePrinterTest extends PrinterTestCase
{
    /**
     * @var AsciiDocOutlineTablePrinter
     */
    private $printer;

    public function test_print_header_should_print_example_table_header()
    {
        $step1 = new StepNode('Given', 'Step 1', [], 1);
        $step2 = new StepNode('Given', 'Step 2', [], 1);
        $step3 = new StepNode('Given', 'Step 3', [], 1);

        $examples = new ExampleTableNode([['Username', 'E-Mail']], 'Examples');
        $feature  = new FeatureNode('', '', [], null, [], 'Feature', 'en', 'feature/my-feature.feature', 1);
        $outline  = new OutlineNode('My Outline', [], [$step1, $step2, $step3], $examples, 'Scenario Outline', 1);

        $this->printer->printHeader($this->formatter, $feature, $outline, []);

        $this->assertOutput("\n|===\n| Username | E-Mail\n");
    }

    public function test_print_footer_closes_the_table()
    {
        /** @var MockObject|TestResult $result */
        $result = $this->createMock(TestResult::class);

        $this->printer->printFooter($this->formatter, $result);

        $this->assertOutput("|===\n");
    }

    protected function setUp()
    {
        parent::setUp();

        $this->printer = new AsciiDocOutlineTablePrinter();
    }
}
