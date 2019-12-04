<?php
declare(strict_types=1);

namespace Digitalnoise\Behat\AsciiDocFormatter\Tests\Printer;

use Behat\Behat\Tester\Result\StepResult;
use Behat\Gherkin\Node\ExampleNode;
use Behat\Gherkin\Node\ExampleTableNode;
use Behat\Gherkin\Node\FeatureNode;
use Behat\Gherkin\Node\OutlineNode;
use Behat\Gherkin\Node\ScenarioLikeInterface;
use Behat\Gherkin\Node\StepNode;
use Behat\Testwork\Output\Formatter;
use Behat\Testwork\Tester\Result\TestResult;
use Digitalnoise\Behat\AsciiDocFormatter\Printer\AsciiDocOutlinePrinter;
use Digitalnoise\Behat\AsciiDocFormatter\Printer\AsciiDocScenarioPrinter;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @author Philip Weinke <philip.weinke@digitalnoise.de>
 */
class AsciiDocOutlinePrinterTest extends PrinterTestCase
{
    /**
     * @var AsciiDocOutlinePrinter
     */
    private $printer;

    public function test_print_header_should_print_example_table_header()
    {
        $examples = new ExampleTableNode([['Username', 'E-Mail']], 'Examples');
        $feature  = new FeatureNode('', '', [], null, [], 'Feature', 'en', 'feature/my-feature.feature', 1);
        $outline  = new OutlineNode('My Outline', [], [], $examples, 'Scenario Outline', 1);

        $this->printer->printHeader($this->formatter, $feature, $outline, $this->createTestResult(TestResult::PASSED));

        $this->assertOutputArray(
            [
                'My Outline',
                '',
                '[cols="4%,~,~", options="header", caption=]',
                '.Examples',
                '|===',
                '| | Username | E-Mail',
            ]
        );
    }

    /**
     * @param TestResult $result
     * @param string     $expectedOutput
     *
     * @dataProvider resultIconDataProvider
     */
    public function test_print_example_should_print_icon_reflecting_the_result($result, $expectedOutput)
    {
        $step1 = new StepNode('Given', '<Username>', [], 2);

        $examples = new ExampleTableNode([['Username']], 'Examples');
        $outline  = new OutlineNode('My Outline', [], [$step1], $examples, 'Scenario Outline', 1);
        $example  = new ExampleNode('', [], [], ['Username' => 'Peter'], 1);

        $stepResults = [2 => $result];

        $this->printer->printExample($this->formatter, $outline, $example, $stepResults);

        $this->assertOutput($expectedOutput);
    }

    /**
     * @return array
     */
    public function resultIconDataProvider(): array
    {
        return [
            'passed' => [
                $this->createStepResult(TestResult::PASSED),
                "| [Example-passed]\n| [Step-passed]Peter\n",
            ],
            'failed' => [
                $this->createStepResult(TestResult::FAILED),
                "| [Example-failed]\n| [Step-failed]Peter\n",
            ],
        ];
    }

    /**
     * @param int $code
     *
     * @return MockObject|TestResult
     */
    protected function createStepResult(int $code)
    {
        $result = $this->createMock(StepResult::class);
        $result->method('getResultCode')->willReturn($code);

        return $result;
    }

    public function test_print_example_should_format_example_values_according_to_their_result()
    {
        $step1 = new StepNode('Given', '<Username>', [], 2);
        $step2 = new StepNode('Given', '<E-Mail>', [], 3);

        $examples = new ExampleTableNode([['Username', 'E-Mail']], 'Examples');
        $outline  = new OutlineNode('My Outline', [], [$step1, $step2], $examples, 'Scenario Outline', 1);
        $example  = new ExampleNode('', [], [], ['Username' => 'Peter', 'E-Mail' => 'peter@peterson.test'], 1);

        $stepResults = [
            2 => $this->createStepResult(TestResult::PASSED),
            3 => $this->createStepResult(TestResult::FAILED),
        ];

        $this->printer->printExample($this->formatter, $outline, $example, $stepResults);

        $this->assertOutputArray(
            [
                "| [Example-failed]",
                "| [Step-passed]Peter",
                "| [Step-failed]peter@peterson.test",
            ]
        );
    }

    public function test_print_example_should_print_stdout_in_a_row_below()
    {
        $step1 = new StepNode('Given', '<Username>', [], 2);
        $step2 = new StepNode('Given', '<E-Mail>', [], 3);

        $examples = new ExampleTableNode([['Username', 'E-Mail']], 'Examples');
        $outline  = new OutlineNode('My Outline', [], [$step1, $step2], $examples, 'Scenario Outline', 1);
        $example  = new ExampleNode('', [], [], ['Username' => 'Peter', 'E-Mail' => 'peter@peterson.test'], 1);

        $stepResults = [
            2 => $this->createExecutedStepResult(null, "Hello\nWorld"),
            3 => $this->createStepResult(TestResult::FAILED),
        ];

        $this->printer->printExample($this->formatter, $outline, $example, $stepResults);

        $this->assertOutputArray(
            [
                ".2+| [Example-failed]",
                "| [Step-passed]Peter",
                "| [Step-failed]peter@peterson.test",
                "2+| Hello",
                "World",
            ]
        );
    }

    public function test_print_example_should_print_exception_in_a_row_below()
    {
        $step1 = new StepNode('Given', '<Username>', [], 2);
        $step2 = new StepNode('Given', '<E-Mail>', [], 3);

        $examples = new ExampleTableNode([['Username', 'E-Mail']], 'Examples');
        $outline  = new OutlineNode('My Outline', [], [$step1, $step2], $examples, 'Scenario Outline', 1);
        $example  = new ExampleNode('', [], [], ['Username' => 'Peter', 'E-Mail' => 'peter@peterson.test'], 1);

        $stepResults = [
            2 => $this->createExecutedStepResult(new Exception('Exception message')),
            3 => $this->createStepResult(TestResult::FAILED),
        ];

        $this->printer->printExample($this->formatter, $outline, $example, $stepResults);

        $this->assertOutputArray(
            [
                ".2+| [Example-failed]",
                "| [Step-failed]Peter",
                "| [Step-failed]peter@peterson.test",
                "2+| Exception message",
            ]
        );
    }

    public function test_print_example_should_print_stdout_and_before_in_the_same_row()
    {
        $step1 = new StepNode('Given', '<Username>', [], 2);
        $step2 = new StepNode('Given', '<E-Mail>', [], 3);

        $examples = new ExampleTableNode([['Username', 'E-Mail']], 'Examples');
        $outline  = new OutlineNode('My Outline', [], [$step1, $step2], $examples, 'Scenario Outline', 1);
        $example  = new ExampleNode('', [], [], ['Username' => 'Peter', 'E-Mail' => 'peter@peterson.test'], 1);

        $stepResults = [
            2 => $this->createExecutedStepResult(null, "Hello\nWorld"),
            3 => $this->createExecutedStepResult(new Exception('Exception message'), "Before exception"),
        ];

        $this->printer->printExample($this->formatter, $outline, $example, $stepResults);

        $this->assertOutputArray(
            [
                ".2+| [Example-failed]",
                "| [Step-passed]Peter",
                "| [Step-failed]peter@peterson.test",
                "2+| Hello",
                "World",
                "Before exception",
                "Exception message",
            ]
        );
    }

    public function test_print_footer_closes_the_table()
    {
        $this->printer->printFooter($this->formatter);

        $this->assertOutput("|===\n");
    }

    protected function setUp()
    {
        parent::setUp();

        /** @var MockObject|AsciiDocScenarioPrinter $scenarioPrinter */
        $scenarioPrinter = $this->createMock(AsciiDocScenarioPrinter::class);
        $scenarioPrinter->method('printScenario')
            ->will(
                self::returnCallback(
                    function (Formatter $formatter, FeatureNode $feature, ScenarioLikeInterface $scenario) {
                        $formatter->getOutputPrinter()->writeln($scenario->getTitle());
                    }
                )
            );

        $this->printer = new AsciiDocOutlinePrinter($scenarioPrinter, new FakeResultFormatter());
    }
}
