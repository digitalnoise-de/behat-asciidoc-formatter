<?php
declare(strict_types=1);

namespace Digitalnoise\BehatAsciiDocFormatter\Tests\Printer;

use Behat\Gherkin\Node\FeatureNode;
use Behat\Testwork\Output\Formatter;
use Digitalnoise\BehatAsciiDocFormatter\Printer\AsciiDocFeaturePrinter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

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

    public function testPrintFeatureTitle()
    {
        $feature = new FeatureNode('My Feature', '', [], null, [], 'Feature', 'en', '', 1);

        $this->printer->printHeader($this->formatter, $feature);

        self::assertEquals("== My Feature\n", $this->outputPrinter->getOutput());
    }

    public function testPrintTags()
    {
        $feature = new FeatureNode('My Feature', '', ['ui', 'registration'], null, [], 'Feature', 'en', '', 1);

        $this->printer->printHeader($this->formatter, $feature);

        self::assertEquals("== My Feature\n[tag]#ui# [tag]#registration#\n", $this->outputPrinter->getOutput());
    }

    public function testPrintDescription()
    {
        $feature = new FeatureNode(
            'My Feature',
            "Multiline\nFeature\nDescription",
            [],
            null,
            [],
            'Feature',
            'en',
            '',
            1
        );

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
