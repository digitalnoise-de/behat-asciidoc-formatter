<?php
declare(strict_types=1);

namespace Digitalnoise\Behat\AsciiDocFormatter\Tests\Printer;

use Behat\Gherkin\Node\BackgroundNode;
use Behat\Gherkin\Node\FeatureNode;
use Behat\Gherkin\Node\ScenarioNode;
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

    public function test_print_header_should_print_title_as_heading()
    {
        $feature  = new FeatureNode('', '', [], null, [], 'Feature', 'en', 'feature/my-feature.feature', 1);
        $scenario = new ScenarioNode('My Scenario', [], [], 'Scenario', 1);

        $this->printer->printHeader($this->formatter, $feature, $scenario);

        $this->assertOutput("==== My Scenario\n\n");
    }

    public function test_print_header_should_print_filename_and_line_as_heading_if_title_is_missing()
    {
        $feature  = new FeatureNode('', '', [], null, [], 'Feature', 'en', 'feature/my-feature.feature', 1);
        $scenario = new ScenarioNode('', [], [], 'Scenario', 1);

        $this->printer->printHeader($this->formatter, $feature, $scenario);

        $this->assertOutput("==== feature/my-feature.feature:1\n\n");
    }

    public function test_print_footer_should_print_a_newline()
    {
        /** @var MockObject|TestResult $result */
        $result = $this->createMock(TestResult::class);

        $this->printer->printFooter($this->formatter, $result);

        $this->assertOutput("\n");
    }

    protected function setUp()
    {
        parent::setUp();

        $this->printer = new AsciiDocScenarioPrinter();
    }
}
