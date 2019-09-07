<?php
declare(strict_types=1);

namespace Digitalnoise\Behat\AsciiDocFormatter\Tests\Printer;

use Behat\Behat\Tester\Result\StepResult;
use Behat\Gherkin\Node\BackgroundNode;
use Behat\Gherkin\Node\FeatureNode;
use Behat\Gherkin\Node\ScenarioNode;
use Behat\Gherkin\Node\StepNode;
use Behat\Testwork\Tester\Result\TestResult;
use Digitalnoise\Behat\AsciiDocFormatter\Printer\AsciiDocScenarioPrinter;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @author Philip Weinke <philip.weinke@digitalnoise.de>
 */
class AsciiDocScenarioPrinterTest extends PrinterTestCase
{
    /**
     * @var AsciiDocScenarioPrinter
     */
    private $printer;

    public function test_title_should_be_formatted()
    {
        $feature  = new FeatureNode('', '', [], null, [], 'Feature', 'en', 'feature/my-feature.feature', 1);
        $scenario = new ScenarioNode('My Scenario', [], [], 'Scenario', 1);
        $result   = $this->createTestResult(TestResult::PASSED);

        $this->printer->printScenario($this->formatter, $feature, $scenario, [], $result);

        $this->assertOutput("==== [Scenario-passed]My Scenario\n\n");
    }

    public function test_filename_should_be_used_as_title_if_there_is_no_title()
    {
        $feature  = new FeatureNode('', '', [], null, [], 'Feature', 'en', 'feature/my-feature.feature', 1);
        $scenario = new ScenarioNode('', [], [], 'Scenario', 1);
        $result   = $this->createTestResult(TestResult::PASSED);

        $this->printer->printScenario($this->formatter, $feature, $scenario, [], $result);

        $this->assertOutput("==== [Scenario-passed]feature/my-feature.feature:1\n\n");
    }

    public function test_steps_should_be_printed_with_the_correct_results()
    {
        $steps = [
            new StepNode('', 'Passing', [], 10),
            new StepNode('', 'Failing', [], 11),
        ];

        $stepResults = [
            10 => $this->createStepResult(TestResult::PASSED),
            11 => $this->createStepResult(TestResult::FAILED),
        ];

        $feature  = new FeatureNode('', '', [], null, [], 'Feature', 'en', 'feature/my-feature.feature', 1);
        $scenario = new ScenarioNode('My Scenario', [], $steps, 'Scenario', 1);
        $result   = $this->createTestResult(TestResult::PASSED);

        $this->printer->printScenario($this->formatter, $feature, $scenario, $stepResults, $result);

        $this->assertOutput("==== [Scenario-passed]My Scenario\n\nPassing:0\nFailing:99\n\n");
    }

    /**
     * @param int $code
     *
     * @return MockObject|StepResult
     */
    private function createStepResult(int $code)
    {
        $result = $this->createMock(StepResult::class);
        $result->method('getResultCode')->willReturn($code);

        return $result;
    }

    public function test_steps_without_result_should_be_printed_as_undefined()
    {
        $steps       = [new StepNode('', 'Passing', [], 10)];
        $stepResults = [];

        $feature  = new FeatureNode('', '', [], null, [], 'Feature', 'en', 'feature/my-feature.feature', 1);
        $scenario = new ScenarioNode('My Scenario', [], $steps, 'Scenario', 1);
        $result   = $this->createTestResult(TestResult::PASSED);

        $this->printer->printScenario($this->formatter, $feature, $scenario, $stepResults, $result);

        $this->assertOutput("==== [Scenario-passed]My Scenario\n\nPassing:30\n\n");
    }

    public function test_background_should_be_printed()
    {
        $steps = [
            new StepNode('', 'Passing', [], 10),
            new StepNode('', 'Failing', [], 11),
        ];

        $stepResults = [
            10 => $this->createStepResult(TestResult::PASSED),
            11 => $this->createStepResult(TestResult::FAILED),
        ];

        $background = new BackgroundNode('', $steps, 'Background', 2);
        $feature    = new FeatureNode('', '', [], $background, [], 'Feature', 'en', 'feature/my-feature.feature', 1);
        $scenario   = new ScenarioNode('My Scenario', [], [], 'Scenario', 1);
        $result     = $this->createTestResult(TestResult::PASSED);

        $this->printer->printScenario($this->formatter, $feature, $scenario, $stepResults, $result);

        $this->assertOutput("==== [Scenario-passed]My Scenario\n\n.Background\nPassing:0\nFailing:99\n\n");
    }

    protected function setUp()
    {
        parent::setUp();

        $this->printer = new AsciiDocScenarioPrinter(new FakeStepPrinter(), new FakeResultFormatter());
    }
}
