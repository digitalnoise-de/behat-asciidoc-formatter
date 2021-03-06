<?php
declare(strict_types=1);

namespace Digitalnoise\Behat\AsciiDocFormatter\Tests\Printer;

use Digitalnoise\Behat\AsciiDocFormatter\Printer\AsciiDocHeaderPrinter;

/**
 * @author Philip Weinke <philip.weinke@digitalnoise.de>
 */
class AsciiDocHeaderPrinterTest extends PrinterTestCase
{
    public function test_print_header_should_print_title_and_toc()
    {
        $printer = new AsciiDocHeaderPrinter('Document Title');

        $printer->printHeader($this->formatter);

        $this->assertOutput(
            "= Document Title\n" .
            ":doctype: book\n" .
            ":icons: font\n" .
            ":toc:\n" .
            ":toclevels: 3\n" .
            ":pdf-theme: behat\n" .
            ":pdf-themesdir: themes\n\n"
        );
    }
}
