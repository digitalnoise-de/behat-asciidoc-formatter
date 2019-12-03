<?php
declare(strict_types=1);

namespace Digitalnoise\Behat\AsciiDocFormatter\Tests\Output;

use Digitalnoise\Behat\AsciiDocFormatter\Output\AsciiDocOutputPrinter;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;

/**
 * @author Philip Weinke <philip.weinke@digitalnoise.de>
 */
class AsciiDocOutputPrinterTest extends TestCase
{
    /**
     * @var AsciiDocOutputPrinter
     */
    private $printer;

    /**
     * @var vfsStreamDirectory
     */
    private $outputDirectory;

    /**
     * @param string $currentFile
     * @param string $includeFile
     * @param string $expected
     *
     * @dataProvider includeDataProvider
     */
    public function test_include_should_include_file_with_relative_path(
        string $currentFile,
        string $includeFile,
        string $expected
    ) {
        $this->printer->setFilename($currentFile);

        $this->printer->include($includeFile);

        self::assertEquals(
            sprintf("include::%s[leveloffset=+1]\n", $expected),
            $this->outputDirectory->getChild($currentFile)->getContent()
        );
    }

    public function includeDataProvider(): array
    {
        return [
            ['test.adoc', 'include.adoc', 'include.adoc'],
            ['folder/test.adoc', 'folder/include.adoc', 'include.adoc'],
            ['folder/test.adoc', 'folder/subfolder/include.adoc', 'subfolder/include.adoc'],
        ];
    }

    protected function setUp()
    {
        parent::setUp();

        $this->outputDirectory = vfsStream::setup();

        $this->printer = new AsciiDocOutputPrinter();
        $this->printer->setOutputPath($this->outputDirectory->url());
    }
}
