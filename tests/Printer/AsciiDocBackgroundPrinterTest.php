<?php
declare(strict_types=1);

namespace Digitalnoise\Behat\AsciiDocFormatter\Tests\Printer;

use Behat\Gherkin\Node\BackgroundNode;
use Behat\Gherkin\Node\StepNode;
use Digitalnoise\Behat\AsciiDocFormatter\Printer\AsciiDocBackgroundPrinter;

/**
 * @author Philip Weinke <philip.weinke@digitalnoise.de>
 */
class AsciiDocBackgroundPrinterTest extends PrinterTestCase
{
    /**
     * @var AsciiDocBackgroundPrinter
     */
    private $printer;

    public function test_print_background_should_print_keyword_as_block_title()
    {
        $step1      = new StepNode('Given', 'Step 1', [], 1);
        $step2      = new StepNode('Given', 'Step 2', [], 1);
        $background = new BackgroundNode('', [$step1, $step2], 'Background', 2);

        $this->printer->printBackground($this->formatter, $background);

        $this->assertOutput(".Background\nStep 1\nStep 2\n\n");
    }

    protected function setUp()
    {
        parent::setUp();

        $this->printer = new AsciiDocBackgroundPrinter(new FakeStepPrinter());
    }
}
