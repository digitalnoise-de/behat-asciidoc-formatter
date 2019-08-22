<?php
declare(strict_types=1);

namespace Digitalnoise\BehatAsciiDocFormatter\Tests\Printer;

use Behat\Behat\Definition\SearchResult;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Tester\Result\ExecutedStepResult;
use Behat\Behat\Tester\Result\SkippedStepResult;
use Behat\Behat\Tester\Result\UndefinedStepResult;
use Behat\Gherkin\Node\ScenarioNode;
use Behat\Gherkin\Node\StepNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Testwork\Call\Call;
use Behat\Testwork\Call\CallResult;
use Behat\Testwork\Output\Formatter;
use Digitalnoise\BehatAsciiDocFormatter\Printer\AsciiDocStepPrinter;
use Exception;
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
        $stepResult = $this->createExecutedStepResult();

        $this->printer->printStep($this->formatter, $this->scenario, $step, $stepResult);

        self::assertEquals("*Given* there is a test +\n", $this->outputPrinter->getOutput());
    }

    private function createExecutedStepResult(Exception $exception = null, $stdOut = null): ExecutedStepResult
    {
        $callResult = new CallResult($this->createMock(Call::class), '', $exception, $stdOut);

        return new ExecutedStepResult(new SearchResult(), $callResult);
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
        $stepResult = $this->createExecutedStepResult();

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

    public function test_print_step_should_print_stdout()
    {
        $step       = new StepNode('Given', 'there is a test', [], 1);
        $stepResult = $this->createExecutedStepResult(null, "Hello\nWorld");

        $this->printer->printStep($this->formatter, $this->scenario, $step, $stepResult);

        self::assertEquals(
            "*Given* there is a test +\n" .
            "----\n" .
            "Hello\n" .
            "World\n" .
            "----\n",
            $this->outputPrinter->getOutput()
        );
    }

    public function test_failed_step_should_be_highlighted_red_and_wrapped_in_a_warning_block()
    {
        $step       = new StepNode('Given', 'there is a test', [], 1);
        $stepResult = $this->createExecutedStepResult(new Exception('Exception message'));

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
        $stepResult = new UndefinedStepResult();

        $this->printer->printStep($this->formatter, $this->scenario, $step, $stepResult);

        self::assertEquals("[yellow]#*Given* there is a test# +\n", $this->outputPrinter->getOutput());
    }

    public function test_pending_step_should_be_highlighted_yellow()
    {
        $step       = new StepNode('Given', 'there is a test', [], 1);
        $stepResult = $this->createExecutedStepResult(new PendingException());

        $this->printer->printStep($this->formatter, $this->scenario, $step, $stepResult);

        self::assertEquals("[yellow]#*Given* there is a test# +\n", $this->outputPrinter->getOutput());
    }

    public function test_skipped_step_should_be_highlighted_blue()
    {
        $step       = new StepNode('Given', 'there is a test', [], 1);
        $stepResult = new SkippedStepResult(new SearchResult());

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
