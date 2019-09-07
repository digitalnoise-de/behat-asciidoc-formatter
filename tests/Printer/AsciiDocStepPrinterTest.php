<?php
declare(strict_types=1);

namespace Digitalnoise\Behat\AsciiDocFormatter\Tests\Printer;

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\ScenarioNode;
use Behat\Gherkin\Node\StepNode;
use Behat\Gherkin\Node\TableNode;
use Digitalnoise\Behat\AsciiDocFormatter\Printer\AsciiDocStepPrinter;
use Exception;

/**
 * @author Philip Weinke <philip.weinke@digitalnoise.de>
 */
class AsciiDocStepPrinterTest extends PrinterTestCase
{
    /**
     * @var ScenarioNode
     */
    private $scenario;

    /**
     * @var AsciiDocStepPrinter
     */
    private $printer;

    public function test_print_step_should_print_step_with_highlighted_keyword()
    {
        $step       = new StepNode('Given', 'there is a test', [], 1);
        $stepResult = $this->createExecutedStepResult();

        $this->printer->printStep($this->formatter, $this->scenario, $step, $stepResult);

        $this->assertOutput("[Step-passed]*Given* there is a test +\n");
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

        $this->assertOutput(
            "[Step-passed]*Given* there are users: +\n" .
            "[stripes=even,options=\"header\"]\n" .
            "|===\n" .
            "| Firstname | Lastname | E-Mail\n" .
            "| Jack | Jackson | jack@jackson.test\n" .
            "| Peter | Peterson | peter@peterson.test\n" .
            "|===\n"
        );
    }

    public function test_print_step_should_print_string_argument()
    {
        $string = new PyStringNode(["Just", "Some", "Text"], 2);

        $step       = new StepNode('Given', 'there is text:', [$string], 1);
        $stepResult = $this->createExecutedStepResult();

        $this->printer->printStep($this->formatter, $this->scenario, $step, $stepResult);

        $this->assertOutput(
            "[Step-passed]*Given* there is text: +\n" .
            "----\n" .
            "Just\n" .
            "Some\n" .
            "Text\n" .
            "----\n"
        );
    }

    public function test_print_step_should_print_stdout()
    {
        $step       = new StepNode('Given', 'there is a test', [], 1);
        $stepResult = $this->createExecutedStepResult(null, "Hello\nWorld");

        $this->printer->printStep($this->formatter, $this->scenario, $step, $stepResult);

        $this->assertOutput(
            "[Step-passed]*Given* there is a test +\n" .
            "----\n" .
            "Hello\n" .
            "World\n" .
            "----\n"
        );
    }

    public function test_failed_step_should_be_wrapped_in_a_warning_block_with_the_exception_message()
    {
        $step       = new StepNode('Given', 'there is a test', [], 1);
        $stepResult = $this->createExecutedStepResult(new Exception('Exception message'));

        $this->printer->printStep($this->formatter, $this->scenario, $step, $stepResult);

        $this->assertOutput(
            "[WARNING]\n" .
            "====\n" .
            "[Step-failed]*Given* there is a test +\n" .
            "----\n" .
            "Exception message\n" .
            "----\n" .
            "====\n"
        );
    }

    public function test_pending_step_should_be_wrapped_in_a_caution_block_with_the_exception_message()
    {
        $step       = new StepNode('Given', 'there is a test', [], 1);
        $stepResult = $this->createExecutedStepResult(new PendingException('Pending message'));

        $this->printer->printStep($this->formatter, $this->scenario, $step, $stepResult);

        $this->assertOutput(
            "[CAUTION]\n" .
            "====\n" .
            "[Step-pending]*Given* there is a test +\n" .
            "----\n" .
            "Pending message\n" .
            "----\n" .
            "====\n"
        );
    }

    protected function setUp()
    {
        parent::setUp();

        $this->scenario = new ScenarioNode('My Scenario', [], [], 'Scenario', 1);

        $this->printer = new AsciiDocStepPrinter(new FakeResultFormatter());
    }
}
