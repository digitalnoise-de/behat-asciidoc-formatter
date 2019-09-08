<?php
declare(strict_types=1);

namespace Digitalnoise\Behat\AsciiDocFormatter\Tests\Output;

use Behat\Behat\Context\Environment\InitializedContextEnvironment;
use Behat\Behat\EventDispatcher\Event\AfterFeatureTested;
use Behat\Behat\EventDispatcher\Event\BeforeFeatureTested;
use Behat\Behat\EventDispatcher\Event\BeforeOutlineTested;
use Behat\Behat\EventDispatcher\Event\BeforeScenarioTested;
use Behat\Gherkin\Node\ExampleNode;
use Behat\Gherkin\Node\ExampleTableNode;
use Behat\Gherkin\Node\FeatureNode;
use Behat\Gherkin\Node\OutlineNode;
use Behat\Gherkin\Node\ScenarioNode;
use Behat\Testwork\EventDispatcher\Event\AfterSuiteTested;
use Behat\Testwork\EventDispatcher\Event\BeforeSuiteTested;
use Behat\Testwork\Output\Formatter;
use Behat\Testwork\Specification\SpecificationArrayIterator;
use Behat\Testwork\Suite\GenericSuite;
use Behat\Testwork\Tester\Result\TestResult;
use Behat\Testwork\Tester\Setup\Teardown;
use Digitalnoise\Behat\AsciiDocFormatter\EventListener\SplitFiles;
use Digitalnoise\Behat\AsciiDocFormatter\Tests\Printer\FakeAsciiDocOutputPrinter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @author Philip Weinke <philip.weinke@digitalnoise.de>
 */
class SplitFilesTest extends TestCase
{
    /**
     * @var FakeAsciiDocOutputPrinter
     */
    private $outputPrinter;

    /**
     * @var MockObject|Formatter
     */
    private $formatter;

    /**
     * @var SplitFiles
     */
    private $listener;

    public function test_suite_should_be_named_after_suite_name()
    {
        $suite = new GenericSuite('My Suite', []);
        $env   = new InitializedContextEnvironment($suite);
        $event = new BeforeSuiteTested($env, new SpecificationArrayIterator($suite));

        $this->listener->listenEvent($this->formatter, $event, '');

        self::assertEquals('my-suite.adoc', $this->outputPrinter->getCurrentFilename());
    }

    public function test_suite_should_include_features()
    {
        $feature1 = new FeatureNode('Feature 1', '', [], null, [], 'Feature', 'en', 'feature/feature-1.feature', 1);
        $feature2 = new FeatureNode('Feature 2', '', [], null, [], 'Feature', 'en', 'feature/feature-1.feature', 1);

        $suite = new GenericSuite('My Suite', []);
        $env   = new InitializedContextEnvironment($suite);
        $event = new AfterSuiteTested(
            $env,
            new SpecificationArrayIterator($suite, [$feature1, $feature2]),
            $this->createMock(TestResult::class),
            $this->createMock(Teardown::class)
        );

        $this->listener->listenEvent($this->formatter, $event, '');

        self::assertEquals(
            "ifndef::no-includes[]\n" .
            "include::my-suite/feature-1.adoc[]\n" .
            "include::my-suite/feature-2.adoc[]\n" .
            "endif::[]\n",
            $this->outputPrinter->getOutput()
        );
    }

    public function test_feature_should_be_named_after_feature_title_and_placed_in_suite_directory()
    {
        $env     = new InitializedContextEnvironment(new GenericSuite('My Suite', []));
        $feature = new FeatureNode('My Feature', '', [], null, [], 'Feature', 'en', 'feature/my-feature.feature', 1);
        $event   = new BeforeFeatureTested($env, $feature);

        $this->listener->listenEvent($this->formatter, $event, '');

        self::assertEquals('my-suite/my-feature.adoc', $this->outputPrinter->getCurrentFilename());
    }

    public function test_feature_should_include_scenarios()
    {
        $scenario1 = new ScenarioNode('', [], [], 'Scenario', 4);
        $scenario2 = new ScenarioNode('', [], [], 'Scenario', 10);
        $scenario3 = new ScenarioNode('', [], [], 'Scenario', 24);

        $env     = new InitializedContextEnvironment(new GenericSuite('My Suite', []));
        $feature = new FeatureNode(
            'My Feature', '', [], null,
            [$scenario1, $scenario2, $scenario3],
            '',
            '',
            '',
            1
        );
        $event   = new AfterFeatureTested(
            $env,
            $feature,
            $this->createMock(TestResult::class),
            $this->createMock(Teardown::class)
        );

        $this->listener->listenEvent($this->formatter, $event, '');

        self::assertEquals(
            "ifndef::no-includes[]\n" .
            "include::my-feature/004.adoc[]\n" .
            "include::my-feature/010.adoc[]\n" .
            "include::my-feature/024.adoc[]\n" .
            "endif::[]\n",
            $this->outputPrinter->getOutput()
        );
    }

    public function test_scenario_should_be_named_after_the_line_number_and_placed_in_scenario_directory()
    {
        $env      = new InitializedContextEnvironment(new GenericSuite('My Suite', []));
        $feature  = new FeatureNode('My Feature', '', [], null, [], 'Feature', 'en', 'feature/my-feature.feature', 1);
        $scenario = new ScenarioNode('', [], [], 'Given', 4);
        $event    = new BeforeScenarioTested($env, $feature, $scenario);

        $this->listener->listenEvent($this->formatter, $event, '');

        self::assertEquals('my-suite/my-feature/004.adoc', $this->outputPrinter->getCurrentFilename());
    }

    public function test_outlines_should_be_named_like_scenarios()
    {
        $env     = new InitializedContextEnvironment(new GenericSuite('My Suite', []));
        $feature = new FeatureNode('My Feature', '', [], null, [], 'Feature', 'en', 'feature/my-feature.feature', 1);
        $outline = new OutlineNode('', [], [], new ExampleTableNode([], 'Examples'), 'Outline', 12);
        $event   = new BeforeOutlineTested($env, $feature, $outline);

        $this->listener->listenEvent($this->formatter, $event, '');

        self::assertEquals('my-suite/my-feature/012.adoc', $this->outputPrinter->getCurrentFilename());
    }

    public function test_examples_should_be_kept_in_the_outlines_file()
    {
        $env     = new InitializedContextEnvironment(new GenericSuite('My Suite', []));
        $feature = new FeatureNode('My Feature', '', [], null, [], 'Feature', 'en', 'feature/my-feature.feature', 1);
        $outline = new OutlineNode('', [], [], new ExampleTableNode([], 'Examples'), 'Outline', 12);
        $event   = new BeforeOutlineTested($env, $feature, $outline);

        $this->listener->listenEvent($this->formatter, $event, '');

        $example = new ExampleNode('', [], [], [], 18);
        $event   = new BeforeScenarioTested($env, $feature, $example);
        $this->listener->listenEvent($this->formatter, $event, '');

        self::assertEquals('my-suite/my-feature/012.adoc', $this->outputPrinter->getCurrentFilename());
    }

    protected function setUp()
    {
        parent::setUp();

        $this->outputPrinter = new FakeAsciiDocOutputPrinter();

        $this->formatter = $this->createMock(Formatter::class);
        $this->formatter->method('getOutputPrinter')->willReturn($this->outputPrinter);

        $this->listener = new SplitFiles();
    }
}
