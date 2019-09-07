<?php
declare(strict_types=1);

namespace Digitalnoise\Behat\AsciiDocFormatter\Printer;

use Behat\Behat\Tester\Result\StepResult;
use Behat\Testwork\Tester\Result\TestResult;

/**
 * @author Philip Weinke <philip.weinke@digitalnoise.de>
 */
class ResultFormatter
{
    private static $codeToString = [
        TestResult::PASSED    => 'passed',
        TestResult::SKIPPED   => 'skipped',
        TestResult::PENDING   => 'pending',
        StepResult::UNDEFINED => 'undefined',
        TestResult::FAILED    => 'failed',
    ];

    /**
     * @var array
     */
    private $formatting;

    /**
     * @param array $formatting
     */
    public function __construct(array $formatting)
    {
        $this->formatting = $formatting;
    }

    /**
     * @param string     $text
     * @param string     $nodeType
     * @param TestResult $testResult
     *
     * @return string
     */
    public function format(string $text, string $nodeType, TestResult $testResult): string
    {
        return sprintf($this->getFormatting($nodeType, $testResult), $text);
    }

    /**
     * @param string     $nodeType
     * @param TestResult $testResult
     *
     * @return string
     */
    private function getFormatting(string $nodeType, TestResult $testResult): string
    {
        $type = strtolower($nodeType);
        if (!array_key_exists($type, $this->formatting)) {
            return '%s';
        }

        $result = $this->getResultString($testResult);
        if (!array_key_exists($result, $this->formatting[$type])) {
            return '%s';
        }

        return $this->formatting[$type][$result];
    }

    /**
     * @param TestResult $testResult
     *
     * @return string
     */
    private function getResultString(TestResult $testResult): string
    {
        return self::$codeToString[$testResult->getResultCode()] ?? '';
    }
}
