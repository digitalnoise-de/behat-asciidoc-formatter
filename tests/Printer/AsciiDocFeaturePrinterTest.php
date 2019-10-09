<?php
declare(strict_types=1);

namespace Digitalnoise\Behat\AsciiDocFormatter\Tests\Printer;

use Behat\Gherkin\Node\FeatureNode;
use Behat\Testwork\Tester\Result\TestResult;
use Digitalnoise\Behat\AsciiDocFormatter\Printer\AsciiDocFeaturePrinter;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @author Philip Weinke <philip.weinke@digitalnoise.de>
 */
class AsciiDocFeaturePrinterTest extends PrinterTestCase
{
    /**
     * @var AsciiDocFeaturePrinter
     */
    private $printer;

    public function test_print_header_should_print_title_as_heading()
    {
        $feature = $this->createFeatureNode('My Feature');

        $this->printer->printHeader($this->formatter, $feature);

        $this->assertOutput("= My Feature\n\n");
    }

    private function createFeatureNode($title, $description = '', array $tags = []): FeatureNode
    {
        return new FeatureNode($title, $description, $tags, null, [], 'Feature', 'en', 'feature/my-feature.feature', 1);
    }

    public function test_print_header_should_use_filename_as_title_if_feature_has_no_title()
    {
        $feature = $this->createFeatureNode(null);

        $this->printer->printHeader($this->formatter, $feature);

        $this->assertOutput("= feature/my-feature.feature\n\n");
    }

    public function test_print_header_should_print_single_line_of_formatted_tags_with_icon()
    {
        $feature = $this->createFeatureNode('My Feature', '', ['ui', 'registration']);

        $this->printer->printHeader($this->formatter, $feature);

        $this->assertOutput("= My Feature\nicon:tags[] ui registration\n\n");
    }

    public function test_print_header_should_print_description_as_a_block()
    {
        $feature = $this->createFeatureNode('My Feature', "Multiline\nFeature\nDescription");

        $this->printer->printHeader($this->formatter, $feature);

        $this->assertOutput("= My Feature\n\n****\nMultiline +\nFeature +\nDescription\n****\n\n");
    }

    protected function setUp()
    {
        parent::setUp();

        $this->printer = new AsciiDocFeaturePrinter();
    }
}
