<?php
declare(strict_types=1);

namespace Digitalnoise\Behat\AsciiDocFormatter\Tests\Printer;

use Behat\Behat\Definition\SearchResult;
use Behat\Behat\Tester\Result\ExecutedStepResult;
use Behat\Testwork\Call\Call;
use Behat\Testwork\Call\CallResult;
use Behat\Testwork\Output\Formatter;
use Behat\Testwork\Tester\Result\TestResult;
use Digitalnoise\Behat\AsciiDocFormatter\Output\AsciiDocOutputPrinter;
use Exception;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @author Philip Weinke <philip.weinke@digitalnoise.de>
 */
abstract class PrinterTestCase extends TestCase
{
    /**
     * @var MockObject|Formatter
     */
    protected $formatter;

    /**
     * @var vfsStreamDirectory
     */
    private $outputDirectory;

    /**
     * @var AsciiDocOutputPrinter
     */
    private $outputPrinter;

    /**
     * @param string $expected
     */
    protected function assertOutput(string $expected): void
    {
        $output = $this->outputDirectory->getChild('output')->getContent();

        self::assertEquals($expected, $output);
    }

    /**
     * @param array $rows
     */
    protected function assertOutputArray(array $rows): void
    {
        $output = $this->outputDirectory->getChild('output')->getContent();
        if ($output[-1] === "\n") {
            $output = substr($output, 0, strlen($output) - 1);
        }

        self::assertEquals($rows, explode("\n", $output));
    }

    protected function setUp()
    {
        parent::setUp();

        $this->outputDirectory = vfsStream::setup();

        $this->outputPrinter = new AsciiDocOutputPrinter();
        $this->outputPrinter->setOutputPath($this->outputDirectory->url());
        $this->outputPrinter->setFilename('output');

        $this->formatter = $this->createMock(Formatter::class);
        $this->formatter->method('getOutputPrinter')->willReturn($this->outputPrinter);
    }

    /**
     * @param int $code
     *
     * @return MockObject|TestResult
     */
    protected function createTestResult(int $code)
    {
        $result = $this->createMock(TestResult::class);
        $result->method('getResultCode')->willReturn($code);

        return $result;
    }

    /**
     * @param Exception|null $exception
     * @param string|null    $stdOut
     *
     * @return ExecutedStepResult
     */
    protected function createExecutedStepResult(Exception $exception = null, string $stdOut = null): ExecutedStepResult
    {
        return new ExecutedStepResult(
            new SearchResult(),
            new CallResult($this->createMock(Call::class), '', $exception, $stdOut)
        );
    }
}
