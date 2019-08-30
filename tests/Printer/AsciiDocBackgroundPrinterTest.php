<?php
declare(strict_types=1);

namespace Digitalnoise\Behat\AsciiDocFormatter\Tests\Printer;

use Behat\Gherkin\Node\BackgroundNode;
use Behat\Gherkin\Node\FeatureNode;
use Behat\Testwork\Tester\Result\TestResult;
use Digitalnoise\Behat\AsciiDocFormatter\Printer\AsciiDocBackgroundPrinter;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @author Philip Weinke <philip.weinke@digitalnoise.de>
 */
class AsciiDocBackgroundPrinterTest extends PrinterTestCase
{
    /**
     * @var AsciiDocBackgroundPrinter
     */
    private $printer;

    public function test_print_header_should_print_keyword_as_block_title()
    {
        $feature    = new FeatureNode('', '', [], null, [], 'Feature', 'en', 'feature/my-feature.feature', 1);
        $background = new BackgroundNode('', [], 'Background', 2);

        $this->printer->printHeader($this->formatter, $feature, $background);

        $this->assertOutput(".Background\n");
    }

    public function test_print_footer_should_print_newline()
    {
        /** @var MockObject|TestResult $result */
        $result = $this->createMock(TestResult::class);

        $this->printer->printFooter($this->formatter, $result);

        $this->assertOutput("\n");
    }

    protected function setUp()
    {
        parent::setUp();

        $this->printer = new AsciiDocBackgroundPrinter();
    }
}
