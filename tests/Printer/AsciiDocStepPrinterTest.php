<?php
declare(strict_types=1);

namespace Digitalnoise\BehatAsciiDocFormatter\Tests\Printer;

use Behat\Behat\Tester\Result\StepResult;
use Behat\Gherkin\Node\ScenarioNode;
use Behat\Gherkin\Node\StepNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Testwork\Output\Formatter;
use Digitalnoise\BehatAsciiDocFormatter\Printer\AsciiDocStepPrinter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class AsciiDocStepPrinterTest extends TestCase
{
    /**
     * @var ScenarioNode
     */
    private $scenario;

    /**
     * @var InMemoryOutputPrinter
     */
    private $outputPrinter;

    /**
     * @var MockObject|Formatter
     */
    private $formatter;

    /**
     * @var AsciiDocStepPrinter
     */
    private $printer;

    public function test_print_step_should_print_step_with_highlighted_keyword()
    {
        $step       = new StepNode('Given', 'there is a test', [], 1);
        $stepResult = $this->createResultMock();

        $this->printer->printStep($this->formatter, $this->scenario, $step, $stepResult);

        self::assertEquals("*Given* there is a test +\n", $this->outputPrinter->getOutput());
    }

    /**
     * @param bool $passed
     * @param int  $resultCode
     *
     * @return MockObject|StepResult
     */
    private function createResultMock(bool $passed = true, int $resultCode = StepResult::PASSED)
    {
        $stepResult = $this->createMock(StepResult::class);
        $stepResult->method('isPassed')->willReturn($passed);
        $stepResult->method('getResultCode')->willReturn($resultCode);

        return $stepResult;
    }

    public function test_print_step_should_print_table_argument()
    {
        $table = new TableNode(
            [
                ['Firstname', 'Lastname', 'E-Mail'],
                ['Jack', 'Jackson', 'jack@jackson.test'],
                ['Peter', 'Peterson', 'peter@peterson.test'],
            ]
        );

        $step       = new StepNode('Given', 'there are users:', [$table], 1);
        $stepResult = $this->createResultMock();

        $this->printer->printStep($this->formatter, $this->scenario, $step, $stepResult);

        self::assertEquals(
            "*Given* there are users: +\n" .
            "[stripes=even,options=\"header\"]\n" .
            "|===\n" .
            "| Firstname | Lastname | E-Mail\n" .
            "| Jack | Jackson | jack@jackson.test\n" .
            "| Peter | Peterson | peter@peterson.test\n" .
            "|===\n",
            $this->outputPrinter->getOutput()
        );
    }

    public function test_failed_step_should_be_highlighted_red_and_wrapped_in_a_warning_block()
    {
        $step       = new StepNode('Given', 'there is a test', [], 1);
        $stepResult = $this->createResultMock(false, StepResult::FAILED);

        $this->printer->printStep($this->formatter, $this->scenario, $step, $stepResult);

        self::assertEquals(
            "[WARNING]\n" .
            "====\n" .
            "[red]#*Given* there is a test# +\n" .
            "====\n",
            $this->outputPrinter->getOutput()
        );
    }

    public function test_undefined_step_should_be_highlighted_yellow()
    {
        $step       = new StepNode('Given', 'there is a test', [], 1);
        $stepResult = $this->createResultMock(false, StepResult::UNDEFINED);

        $this->printer->printStep($this->formatter, $this->scenario, $step, $stepResult);

        self::assertEquals("[yellow]#*Given* there is a test# +\n", $this->outputPrinter->getOutput());
    }

    public function test_pending_step_should_be_highlighted_yellow()
    {
        $step       = new StepNode('Given', 'there is a test', [], 1);
        $stepResult = $this->createResultMock(false, StepResult::PENDING);

        $this->printer->printStep($this->formatter, $this->scenario, $step, $stepResult);

        self::assertEquals("[yellow]#*Given* there is a test# +\n", $this->outputPrinter->getOutput());
    }

    public function test_skipped_step_should_be_highlighted_blue()
    {
        $step       = new StepNode('Given', 'there is a test', [], 1);
        $stepResult = $this->createResultMock(false, StepResult::SKIPPED);

        $this->printer->printStep($this->formatter, $this->scenario, $step, $stepResult);

        self::assertEquals("[blue]#*Given* there is a test# +\n", $this->outputPrinter->getOutput());
    }

    protected function setUp()
    {
        parent::setUp();

        $this->scenario = new ScenarioNode('My Scenario', [], [], 'Scenario', 1);

        $this->outputPrinter = new InMemoryOutputPrinter();

        $this->formatter = $this->createMock(Formatter::class);
        $this->formatter->method('getOutputPrinter')->willReturn($this->outputPrinter);

        $this->printer = new AsciiDocStepPrinter();
    }
}
