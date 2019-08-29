<?php
declare(strict_types=1);

namespace Digitalnoise\Behat\AsciiDocFormatter\Tests\Printer;

use Behat\Gherkin\Node\BackgroundNode;
use Behat\Gherkin\Node\StepNode;
use Behat\Testwork\Output\Formatter;
use Digitalnoise\Behat\AsciiDocFormatter\Printer\AsciiDocBackgroundPrinter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @author Philip Weinke <philip.weinke@digitalnoise.de>
 */
class AsciiDocBackgroundPrinterTest extends TestCase
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
     * @var AsciiDocBackgroundPrinter
     */
    private $printer;

    public function test_print_background_should_print_keyword_as_block_title()
    {
//        $feature    = new FeatureNode('', '', [], null, [], 'Feature', 'en', 'feature/my-feature.feature', 1);
        $step1      = new StepNode('Given', 'Step 1', [], 1);
        $step2      = new StepNode('Given', 'Step 2', [], 1);
        $background = new BackgroundNode('', [$step1, $step2], 'Background', 2);

        $this->printer->printBackground($this->formatter, $background);

        self::assertEquals(".Background\nStep 1\nStep 2\n\n", $this->outputPrinter->getOutput());
    }

    protected function setUp()
    {
        parent::setUp();

        $this->outputPrinter = new FakeAsciiDocOutputPrinter();

        $this->formatter = $this->createMock(Formatter::class);
        $this->formatter->method('getOutputPrinter')->willReturn($this->outputPrinter);

        $this->printer = new AsciiDocBackgroundPrinter(new FakeStepPrinter());
    }
}
