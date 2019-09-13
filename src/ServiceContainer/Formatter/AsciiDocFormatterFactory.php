<?php
declare(strict_types=1);

namespace Digitalnoise\Behat\AsciiDocFormatter\ServiceContainer\Formatter;

use Behat\Behat\EventDispatcher\Event\OutlineTested;
use Behat\Behat\EventDispatcher\Event\ScenarioTested;
use Behat\Behat\Output\Node\EventListener\AST\FeatureListener;
use Behat\Behat\Output\Node\EventListener\Flow\FireOnlySiblingsListener;
use Behat\Testwork\Output\Node\EventListener\ChainEventListener;
use Behat\Testwork\Output\NodeEventListeningFormatter;
use Behat\Testwork\Output\ServiceContainer\Formatter\FormatterFactory;
use Behat\Testwork\Output\ServiceContainer\OutputExtension;
use Digitalnoise\Behat\AsciiDocFormatter\EventListener\ExerciseListener;
use Digitalnoise\Behat\AsciiDocFormatter\EventListener\FileSplitter;
use Digitalnoise\Behat\AsciiDocFormatter\EventListener\OutlineListener;
use Digitalnoise\Behat\AsciiDocFormatter\EventListener\Resetter;
use Digitalnoise\Behat\AsciiDocFormatter\EventListener\ScenarioListener;
use Digitalnoise\Behat\AsciiDocFormatter\EventListener\SuiteListener;
use Digitalnoise\Behat\AsciiDocFormatter\Output\AsciiDocOutputPrinter;
use Digitalnoise\Behat\AsciiDocFormatter\Output\FileNamer;
use Digitalnoise\Behat\AsciiDocFormatter\Output\OutputDirectoryResetter;
use Digitalnoise\Behat\AsciiDocFormatter\Printer\AsciiDocFeaturePrinter;
use Digitalnoise\Behat\AsciiDocFormatter\Printer\AsciiDocHeaderPrinter;
use Digitalnoise\Behat\AsciiDocFormatter\Printer\AsciiDocOutlinePrinter;
use Digitalnoise\Behat\AsciiDocFormatter\Printer\AsciiDocScenarioPrinter;
use Digitalnoise\Behat\AsciiDocFormatter\Printer\AsciiDocSetupPrinter;
use Digitalnoise\Behat\AsciiDocFormatter\Printer\AsciiDocStepPrinter;
use Digitalnoise\Behat\AsciiDocFormatter\Printer\AsciiDocSuitePrinter;
use Digitalnoise\Behat\AsciiDocFormatter\Printer\ResultFormatter;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Philip Weinke <philip.weinke@digitalnoise.de>
 */
class AsciiDocFormatterFactory implements FormatterFactory
{
    public const ROOT_LISTENER_ID = 'asciidoc.node.listener';

    public const RESULT_FORMATTER_ID = 'asciidoc.result_formatter';

    public const HEADER_PRINTER_ID = 'asciidoc.printer.header';
    public const SETUP_PRINTER_ID = 'asciidoc.printer.setup';
    public const SUITE_PRINTER_ID = 'asciidoc.printer.suite';
    public const FEATURE_PRINTER_ID = 'asciidoc.printer.feature';
    public const OUTLINE_PRINTER_ID = 'asciidoc.printer.outline';
    public const SCENARIO_PRINTER_ID = 'asciidoc.printer.scenario';
    public const STEP_PRINTER_ID = 'asciidoc.printer.step';

    /**
     * @param ContainerBuilder $container
     */
    public function buildFormatter(ContainerBuilder $container)
    {
        $this->loadRootNodeListener($container);
        $this->loadPrinter($container);

        $definition = new Definition(
            NodeEventListeningFormatter::class,
            [
                'asciidoc',
                'Outputs in asciidoc.',
                [],
                $this->createOutputPrinterDefinition(),
                new Reference(self::ROOT_LISTENER_ID),
            ]
        );

        $definition->addTag(OutputExtension::FORMATTER_TAG, ['priority' => 100]);
        $container->setDefinition(OutputExtension::FORMATTER_TAG . '.asciidoc', $definition);
    }

    /**
     * @param ContainerBuilder $container
     */
    private function loadRootNodeListener(ContainerBuilder $container): void
    {
        $definition = new Definition(
            ChainEventListener::class,
            [
                [
                    new Definition(
                        Resetter::class,
                        [new Definition(OutputDirectoryResetter::class, [__DIR__ . '/../../../themes'])]
                    ),
                    new Definition(FileSplitter::class, [new Definition(FileNamer::class)]),
                    new Definition(ExerciseListener::class, [new Reference(self::HEADER_PRINTER_ID)]),
                    new Definition(SuiteListener::class, [new Reference(self::SUITE_PRINTER_ID)]),
                    new Definition(
                        FeatureListener::class,
                        [new Reference(self::FEATURE_PRINTER_ID), new Reference(self::SETUP_PRINTER_ID)]
                    ),
                    $this->proxySiblingEvents(
                        OutlineTested::BEFORE,
                        OutlineTested::AFTER,
                        [
                            new Definition(OutlineListener::class, [new Reference(self::OUTLINE_PRINTER_ID)]),
                        ]
                    ),
                    $this->proxySiblingEvents(
                        ScenarioTested::BEFORE,
                        ScenarioTested::AFTER,
                        [
                            new Definition(ScenarioListener::class, [new Reference(self::SCENARIO_PRINTER_ID)]),
                        ]
                    ),
                ],
            ]
        );

        $container->setDefinition(self::ROOT_LISTENER_ID, $definition);
    }

    /**
     * @param string       $beforeEventName
     * @param string       $afterEventName
     * @param Definition[] $listeners
     *
     * @return Definition
     */
    private function proxySiblingEvents(string $beforeEventName, string $afterEventName, array $listeners): Definition
    {
        return new Definition(
            FireOnlySiblingsListener::class,
            [
                $beforeEventName,
                $afterEventName,
                new Definition(ChainEventListener::class, [$listeners]),
            ]
        );
    }

    /**
     * @param ContainerBuilder $container
     */
    private function loadPrinter(ContainerBuilder $container): void
    {
        $container->setDefinition(
            self::RESULT_FORMATTER_ID,
            new Definition(ResultFormatter::class, ['%asciidoc.formatting%'])
        );

        $container->setDefinition(
            self::HEADER_PRINTER_ID,
            new Definition(AsciiDocHeaderPrinter::class, ['%asciidoc.title%'])
        );

        $container->setDefinition(self::SETUP_PRINTER_ID, new Definition(AsciiDocSetupPrinter::class));
        $container->setDefinition(self::FEATURE_PRINTER_ID, new Definition(AsciiDocFeaturePrinter::class));
        $container->setDefinition(self::SUITE_PRINTER_ID, new Definition(AsciiDocSuitePrinter::class));

        $container->setDefinition(
            self::STEP_PRINTER_ID,
            new Definition(AsciiDocStepPrinter::class, [new Reference(self::RESULT_FORMATTER_ID)])
        );

        $container->setDefinition(
            self::SCENARIO_PRINTER_ID,
            new Definition(
                AsciiDocScenarioPrinter::class,
                [
                    new Reference(self::STEP_PRINTER_ID),
                    new Reference(self::RESULT_FORMATTER_ID),
                ]
            )
        );

        $container->setDefinition(
            self::OUTLINE_PRINTER_ID,
            new Definition(
                AsciiDocOutlinePrinter::class,
                [
                    new Reference(self::SCENARIO_PRINTER_ID),
                    new Reference(self::RESULT_FORMATTER_ID),
                ]
            )
        );
    }

    /**
     * @return Definition
     */
    private function createOutputPrinterDefinition(): Definition
    {
        return new Definition(AsciiDocOutputPrinter::class);
    }

    /**
     * @param ContainerBuilder $container
     */
    public function processFormatter(ContainerBuilder $container)
    {
    }
}
