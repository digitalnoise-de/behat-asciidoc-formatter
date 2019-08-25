<?php
declare(strict_types=1);

namespace Digitalnoise\Behat\AsciiDocFormatter\Tests\Printer;

use Behat\Behat\Output\Node\Printer\ScenarioPrinter;
use Behat\Behat\Output\Node\Printer\StepPrinter;
use Behat\Behat\Tester\Result\StepResult;
use Behat\Gherkin\Node\ExampleTableNode;
use Behat\Gherkin\Node\FeatureNode;
use Behat\Gherkin\Node\OutlineNode;
use Behat\Gherkin\Node\ScenarioLikeInterface as Scenario;
use Behat\Gherkin\Node\StepNode;
use Behat\Testwork\Output\Formatter;
use Behat\Testwork\Tester\Result\TestResult;
use Digitalnoise\Behat\AsciiDocFormatter\Printer\AsciiDocOutlineTablePrinter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @author Philip Weinke <philip.weinke@digitalnoise.de>
 */
class AsciiDocOutlineTablePrinterTest extends TestCase
{
    /**
     * @var InMemoryAsciiDocOutputPrinter
     */
    private $outputPrinter;

    /**
     * @var MockObject|Formatter
     */
    private $formatter;

    /**
     * @var MockObject|ScenarioPrinter
     */
    private $scenarioPrinter;

    /**
     * @var MockObject|StepPrinter
     */
    private $stepPrinter;

    /**
     * @var AsciiDocOutlineTablePrinter
     */
    private $printer;

    public function test_print_header_should_print_title_steps_and_example_table_header()
    {
        $step1 = new StepNode('Given', 'Step 1', [], 1);
        $step2 = new StepNode('Given', 'Step 2', [], 1);
        $step3 = new StepNode('Given', 'Step 3', [], 1);

        $examples = new ExampleTableNode([['Username', 'E-Mail']], 'Examples');
        $feature  = new FeatureNode('', '', [], null, [], 'Feature', 'en', 'feature/my-feature.feature', 1);
        $outline  = new OutlineNode('My Outline', [], [$step1, $step2, $step3], $examples, 'Scenario Outline', 1);

        $this->printer->printHeader($this->formatter, $feature, $outline, []);

        self::assertEquals(
            "My Outline\n" .
            "Step 1\n" .
            "Step 2\n" .
            "Step 3\n\n" .
            "|===\n" .
            "| Username | E-Mail\n",
            $this->outputPrinter->getOutput()
        );
    }

    public function test_print_footer_closes_the_table()
    {
        $this->printer->printFooter($this->formatter, $this->createMock(TestResult::class));

        self::assertEquals("|===\n\n", $this->outputPrinter->getOutput());
    }

    protected function setUp()
    {
        parent::setUp();

        $this->outputPrinter = new InMemoryAsciiDocOutputPrinter();

        $this->formatter = $this->createMock(Formatter::class);
        $this->formatter->method('getOutputPrinter')->willReturn($this->outputPrinter);

        $this->createScenarioPrinter();
        $this->createStepPrinter();

        $this->printer = new AsciiDocOutlineTablePrinter($this->scenarioPrinter, $this->stepPrinter);
    }

    private function createScenarioPrinter()
    {
        $this->scenarioPrinter = $this->createMock(ScenarioPrinter::class);
        $this->scenarioPrinter
            ->method('printHeader')
            ->will(
                self::returnCallback(
                    function (Formatter $formatter, FeatureNode $featureNode, OutlineNode $scenario) {
                        $this->formatter->getOutputPrinter()->writeln($scenario->getTitle());
                    }
                )
            );
    }

    private function createStepPrinter()
    {
        $this->stepPrinter = $this->createMock(StepPrinter::class);
        $this->stepPrinter
            ->method('printStep')
            ->will(
                self::returnCallback(
                    function (Formatter $formatter, Scenario $scenario, StepNode $step, StepResult $result) {
                        $this->formatter->getOutputPrinter()->writeln($step->getText());
                    }
                )
            );
    }
}
