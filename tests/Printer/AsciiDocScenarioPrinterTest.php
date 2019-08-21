<?php
declare(strict_types=1);

namespace Digitalnoise\BehatAsciiDocFormatter\Tests\Printer;

use Behat\Gherkin\Node\FeatureNode;
use Behat\Gherkin\Node\ScenarioNode;
use Behat\Testwork\Output\Formatter;
use Digitalnoise\BehatAsciiDocFormatter\Printer\AsciiDocScenarioPrinter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class AsciiDocScenarioPrinterTest
 *
 * @author Philip Weinke <philip.weinke@digitalnoise.de>
 */
class AsciiDocScenarioPrinterTest extends TestCase
{
    /**
     * @var InMemoryOutputPrinter
     */
    private $outputPrinter;

    /**
     * @var MockObject|Formatter
     */
    private $formatter;

    /**
     * @var AsciiDocScenarioPrinter
     */
    private $printer;

    public function test_print_header_should_print_title_as_heading()
    {
        $feature  = new FeatureNode('', '', [], null, [], 'Feature', 'en', 'feature/my-feature.feature', 1);
        $scenario = new ScenarioNode('My Scenario', [], [], 'Scenario', 1);

        $this->printer->printHeader($this->formatter, $feature, $scenario);

        self::assertEquals("=== My Scenario\n", $this->outputPrinter->getOutput());
    }

    protected function setUp()
    {
        parent::setUp();

        $this->outputPrinter = new InMemoryOutputPrinter();

        $this->formatter = $this->createMock(Formatter::class);
        $this->formatter->method('getOutputPrinter')->willReturn($this->outputPrinter);

        $this->printer = new AsciiDocScenarioPrinter();
    }
}
