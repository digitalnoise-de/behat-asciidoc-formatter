<?php
declare(strict_types=1);

namespace Digitalnoise\Behat\AsciiDocFormatter\Tests\EventListener;

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
use Digitalnoise\Behat\AsciiDocFormatter\EventListener\FileSplitter;
use Digitalnoise\Behat\AsciiDocFormatter\Output\AsciiDocOutputPrinter;
use Exception;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @author Philip Weinke <philip.weinke@digitalnoise.de>
 */
class FileSplitterTest extends TestCase
{
    /**
     * @var vfsStreamDirectory
     */
    private $outputDirectory;

    /**
     * @var AsciiDocOutputPrinter
     */
    private $outputPrinter;

    /**
     * @var MockObject|Formatter
     */
    private $formatter;

    /**
     * @var FileSplitter
     */
    private $listener;

    public function test_suite_should_be_named()
    {
        $suite = new GenericSuite('My Suite', []);
        $env   = new InitializedContextEnvironment($suite);
        $event = new BeforeSuiteTested($env, new SpecificationArrayIterator($suite));

        $this->listener->listenEvent($this->formatter, $event, '');

        $this->assertFilename('My Suite');
    }

    /**
     * @param string $filename
     *
     * @throws Exception
     */
    private function assertFilename(string $filename): void
    {
        self::assertTrue($this->outputDirectory->hasChild($filename), 'File does not exist');
    }

    public function test_suite_should_include_features_with_leveloffset()
    {
        $suite = new GenericSuite('My Suite', []);
        $env   = new InitializedContextEnvironment($suite);
        $event = new AfterSuiteTested(
            $env,
            new SpecificationArrayIterator(
                $suite,
                [$this->createFeature('Feature 1'), $this->createFeature('Feature 2')]
            ),
            $this->createMock(TestResult::class),
            $this->createMock(Teardown::class)
        );

        $this->listener->listenEvent($this->formatter, $event, '');

        $this->assertContent(
            "ifndef::no-includes[]\n" .
            "include::My Suite/Feature 1[leveloffset=+1]\n" .
            "include::My Suite/Feature 2[leveloffset=+1]\n" .
            "endif::[]\n",
            'My Suite'
        );
    }

    /**
     * @param string $title
     * @param array  $scenarios
     *
     * @return FeatureNode
     */
    private function createFeature(string $title, array $scenarios = []): FeatureNode
    {
        return new FeatureNode($title, '', [], null, $scenarios, '', '', '', 1);
    }

    /**
     * @param string $content
     * @param string $filename
     */
    private function assertContent(string $content, string $filename): void
    {
        self::assertEquals($content, $this->outputDirectory->getChild($filename)->getContent());
    }

    public function test_feature_should_be_named_after_feature_and_suite()
    {
        $env   = new InitializedContextEnvironment(new GenericSuite('My Suite', []));
        $event = new BeforeFeatureTested($env, $this->createFeature('My Feature'));

        $this->listener->listenEvent($this->formatter, $event, '');

        $this->assertFilename('My Suite/My Feature');
    }

    public function test_feature_should_include_scenarios_with_leveloffset()
    {
        $env     = new InitializedContextEnvironment(new GenericSuite('My Suite', []));
        $feature = $this->createFeature('My Feature', [$this->createScenario('1'), $this->createScenario('2')]);
        $event   = new AfterFeatureTested(
            $env,
            $feature,
            $this->createMock(TestResult::class),
            $this->createMock(Teardown::class)
        );

        $this->listener->listenEvent($this->formatter, $event, '');

        $this->assertContent(
            "ifndef::no-includes[]\n" .
            "include::My Feature/1[leveloffset=+1]\n" .
            "include::My Feature/2[leveloffset=+1]\n" .
            "endif::[]\n",
            'My Suite/My Feature'
        );
    }

    private function createScenario(string $title): ScenarioNode
    {
        return new ScenarioNode($title, [], [], '', 1);
    }

    public function test_scenario_should_be_named_after_suite_feature_and_scenario()
    {
        $env      = new InitializedContextEnvironment(new GenericSuite('My Suite', []));
        $feature  = new FeatureNode('My Feature', '', [], null, [], 'Feature', 'en', 'feature/my-feature.feature', 1);
        $scenario = new ScenarioNode('My Scenario', [], [], 'Given', 4);
        $event    = new BeforeScenarioTested($env, $feature, $scenario);

        $this->listener->listenEvent($this->formatter, $event, '');

        $this->assertFilename('My Suite/My Feature/My Scenario');
    }

    public function test_outlines_should_be_named_like_scenarios()
    {
        $env     = new InitializedContextEnvironment(new GenericSuite('My Suite', []));
        $feature = $this->createFeature('My Feature');
        $outline = new OutlineNode('My Outline', [], [], new ExampleTableNode([], 'Examples'), 'Outline', 12);
        $event   = new BeforeOutlineTested($env, $feature, $outline);

        $this->listener->listenEvent($this->formatter, $event, '');

        $this->assertFilename('My Suite/My Feature/My Outline');
    }

    public function test_examples_should_be_kept_in_the_outlines_file()
    {
        $env     = new InitializedContextEnvironment(new GenericSuite('My Suite', []));
        $feature = $this->createFeature('My Feature');
        $outline = new OutlineNode('My Outline', [], [], new ExampleTableNode([], 'Examples'), 'Outline', 12);
        $event   = new BeforeOutlineTested($env, $feature, $outline);

        $this->listener->listenEvent($this->formatter, $event, '');

        $example = new ExampleNode('', [], [], [], 18);
        $event   = new BeforeScenarioTested($env, $feature, $example);
        $this->listener->listenEvent($this->formatter, $event, '');

        $this->assertFilename('My Suite/My Feature/My Outline');
    }

    protected function setUp()
    {
        parent::setUp();

        $this->outputDirectory = vfsStream::setup();

        $this->outputPrinter = new AsciiDocOutputPrinter();
        $this->outputPrinter->setOutputPath($this->outputDirectory->url());

        $this->formatter = $this->createMock(Formatter::class);
        $this->formatter->method('getOutputPrinter')->willReturn($this->outputPrinter);

        $this->listener = new FileSplitter(new FakeFileNamer());
    }
}
