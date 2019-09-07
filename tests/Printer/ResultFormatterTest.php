<?php

namespace Digitalnoise\Behat\AsciiDocFormatter\Tests\Printer;

use Behat\Testwork\Tester\Result\TestResult;
use Digitalnoise\Behat\AsciiDocFormatter\Printer\ResultFormatter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @author Philip Weinke <philip.weinke@digitalnoise.de>
 */
class ResultFormatterTest extends TestCase
{
    /**
     * @param string $input
     * @param string $nodeType
     * @param int    $resultCode
     * @param string $expectedOutput
     *
     * @dataProvider formatDataProvider
     */
    public function test_formatting(string $input, string $nodeType, int $resultCode, string $expectedOutput)
    {
        $formatter = new ResultFormatter(['scenario' => ['passed' => 'P:%s']]);

        $output = $formatter->format($input, $nodeType, $this->createTestResult($resultCode));

        self::assertEquals($expectedOutput, $output);
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
     * @return array
     */
    public function formatDataProvider(): array
    {
        return [
            'No format defined'         => ['text', 'Outline', TestResult::PASSED, 'text'],
            'Node and code defined'     => ['text', 'Scenario', TestResult::PASSED, 'P:text'],
            'Node defined but not code' => ['text', 'Scenario', TestResult::SKIPPED, 'text'],
        ];
    }
}
