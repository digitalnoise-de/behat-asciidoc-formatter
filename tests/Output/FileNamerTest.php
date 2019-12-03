<?php

namespace Digitalnoise\Behat\AsciiDocFormatter\Tests\Output;

use Behat\Gherkin\Node\FeatureNode;
use Behat\Gherkin\Node\ScenarioNode;
use Behat\Testwork\Suite\GenericSuite;
use Digitalnoise\Behat\AsciiDocFormatter\Output\FileNamer;
use PHPUnit\Framework\TestCase;

class FileNamerTest extends TestCase
{
    /**
     * @var FileNamer
     */
    private $fileNamer;

    public function test_suite_should_be_named_after_suite_name()
    {
        $suite = new GenericSuite('My Suite', []);

        self::assertEquals(
            'suites/my-suite.adoc',
            $this->fileNamer->nameFor($suite)
        );
    }

    public function test_feature_should_be_named_after_feature_file_and_placed_in_suite_directory()
    {
        $suite   = new GenericSuite('My Suite', []);
        $feature = new FeatureNode('My Feature', '', [], null, [], '', '', 'my_feature_file.feature', 1);

        self::assertEquals(
            'suites/my-suite/my-feature-file.adoc',
            $this->fileNamer->nameFor($suite, $feature)
        );
    }

    public function test_scenario_should_be_named_after_scenario_line_number_and_placed_in_feature_directory()
    {
        $suite    = new GenericSuite('My Suite', []);
        $feature  = new FeatureNode('My Feature', '', [], null, [], '', '', 'my_feature_file.feature', 1);
        $scenario = new ScenarioNode('My Scenario', [], [], 'Given', 10);

        self::assertEquals(
            'suites/my-suite/my-feature-file/010.adoc',
            $this->fileNamer->nameFor($suite, $feature, $scenario)
        );
    }

    protected function setUp()
    {
        parent::setUp();

        $this->fileNamer = new FileNamer();
    }
}
