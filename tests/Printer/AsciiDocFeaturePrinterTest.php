<?php
declare(strict_types=1);

namespace Digitalnoise\BehatAsciiDocFormatter\Tests\Printer;

use Behat\Gherkin\Node\FeatureNode;
use Behat\Testwork\Output\Formatter;
use Digitalnoise\BehatAsciiDocFormatter\Printer\AsciiDocFeaturePrinter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class AsciiDocFeaturePrinterTest
 *
 * @author Philip Weinke <philip.weinke@digitalnoise.de>
 */
class AsciiDocFeaturePrinterTest extends TestCase
{
    /**
     * @var AsciiDocFeaturePrinter
     */
    private $printer;

    /**
     * @var InMemoryOutputPrinter
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

        self::assertEquals("== My Feature\n", $this->outputPrinter->getOutput());
    }

    private function createFeatureNode($title, $description = '', array $tags = []): FeatureNode
    {
        return new FeatureNode($title, $description, $tags, null, [], 'Feature', 'en', 'feature/my-feature.feature', 1);
    }

    public function test_print_header_should_use_filename_as_title_if_feature_has_no_title()
    {
        $feature = $this->createFeatureNode(null);

        $this->printer->printHeader($this->formatter, $feature);

        self::assertEquals("== feature/my-feature.feature\n", $this->outputPrinter->getOutput());
    }

    public function test_print_header_should_print_single_line_of_formatted_tags_with_icon()
    {
        $feature = $this->createFeatureNode('My Feature', '', ['ui', 'registration']);

        $this->printer->printHeader($this->formatter, $feature);

        self::assertEquals("== My Feature\nicon:tags[] ui registration\n\n", $this->outputPrinter->getOutput());
    }

    public function test_print_header_should_print_description_as_a_block()
    {
        $feature = $this->createFeatureNode('My Feature', "Multiline\nFeature\nDescription");

        $this->printer->printHeader($this->formatter, $feature);

        self::assertEquals(
            "== My Feature\n****\nMultiline +\nFeature +\nDescription\n****\n",
            $this->outputPrinter->getOutput()
        );
    }

    protected function setUp()
    {
        parent::setUp();

        $this->outputPrinter = new InMemoryOutputPrinter();

        $this->formatter = $this->createMock(Formatter::class);
        $this->formatter->method('getOutputPrinter')->willReturn($this->outputPrinter);

        $this->printer = new AsciiDocFeaturePrinter();
    }
}
