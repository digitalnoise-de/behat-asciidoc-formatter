<?php
declare(strict_types=1);

namespace Digitalnoise\Behat\AsciiDocFormatter\Tests\Printer;

use Behat\Behat\Tester\Result\StepResult;
use Behat\Testwork\Tester\Result\TestResult;
use Digitalnoise\Behat\AsciiDocFormatter\Printer\ResultFormatter;

class FakeResultFormatter extends ResultFormatter
{
    private static $codeToString = [
        TestResult::PASSED    => 'passed',
        TestResult::SKIPPED   => 'skipped',
        TestResult::PENDING   => 'pending',
        StepResult::UNDEFINED => 'undefined',
        TestResult::FAILED    => 'failed',
    ];

    /**
     */
    public function __construct()
    {
        parent::__construct([]);
    }

    public function format(string $text, string $nodeType, TestResult $testResult): string
    {
        return sprintf('[%s-%s]%s', $nodeType, self::$codeToString[$testResult->getResultCode()], $text);
    }
}
