<?php
declare(strict_types=1);

namespace Digitalnoise\Behat\AsciiDocFormatter\Tests\Printer;

use Behat\Gherkin\Node\FeatureNode;
use Behat\Testwork\Output\Formatter;
use Behat\Testwork\Tester\Result\TestResult;
use Digitalnoise\Behat\AsciiDocFormatter\Printer\AsciiDocFeaturePrinter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @author Philip Weinke <philip.weinke@digitalnoise.de>
 */
class AsciiDocFeaturePrinterTest extends TestCase
{
    /**
     * @var AsciiDocFeaturePrinter
     */
    private $printer;

    /**
     * @var FakeAsciiDocOutputPrinter
     */
    private $outputPrinter;

    /**
     * @var MockObject|Formatter
     */
    private $formatter;

    public function test_print_header_should_print_title_as_heading()
    {
        $feature = $this->createFeatureNode('My Feature');

        $this->printer->printHeader($this->formatter, $feature);

        self::assertEquals("=== My Feature\n\n", $this->outputPrinter->getOutput());
    }

    private function createFeatureNode($title, $description = '', array $tags = []): FeatureNode
    {
        return new FeatureNode($title, $description, $tags, null, [], 'Feature', 'en', 'feature/my-feature.feature', 1);
    }

    public function test_print_header_should_use_filename_as_title_if_feature_has_no_title()
    {
        $feature = $this->createFeatureNode(null);

        $this->printer->printHeader($this->formatter, $feature);

        self::assertEquals("=== feature/my-feature.feature\n\n", $this->outputPrinter->getOutput());
    }

    public function test_print_header_should_print_single_line_of_formatted_tags_with_icon()
    {
        $feature = $this->createFeatureNode('My Feature', '', ['ui', 'registration']);

        $this->printer->printHeader($this->formatter, $feature);

        self::assertEquals("=== My Feature\nicon:tags[] ui registration\n\n", $this->outputPrinter->getOutput());
    }

    public function test_print_header_should_print_description_as_a_block()
    {
        $feature = $this->createFeatureNode('My Feature', "Multiline\nFeature\nDescription");

        $this->printer->printHeader($this->formatter, $feature);

        self::assertEquals(
            "=== My Feature\n\n****\nMultiline +\nFeature +\nDescription\n****\n\n",
            $this->outputPrinter->getOutput()
        );
    }

    public function test_print_footer_should_print_a_page_break()
    {
        $this->printer->printFooter($this->formatter, $this->createMock(TestResult::class));

        self::assertEquals("<<<\n", $this->outputPrinter->getOutput());
    }

    protected function setUp()
    {
        parent::setUp();

        $this->outputPrinter = new FakeAsciiDocOutputPrinter();

        $this->formatter = $this->createMock(Formatter::class);
        $this->formatter->method('getOutputPrinter')->willReturn($this->outputPrinter);

        $this->printer = new AsciiDocFeaturePrinter();
    }
}
