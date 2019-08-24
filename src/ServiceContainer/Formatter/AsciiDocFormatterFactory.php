<?php
declare(strict_types=1);

namespace Digitalnoise\Behat\AsciiDocFormatter\ServiceContainer\Formatter;

use Behat\Behat\EventDispatcher\Event\BackgroundTested;
use Behat\Behat\EventDispatcher\Event\OutlineTested;
use Behat\Behat\EventDispatcher\Event\ScenarioTested;
use Behat\Behat\Output\Node\EventListener\AST\FeatureListener;
use Behat\Behat\Output\Node\EventListener\AST\OutlineTableListener;
use Behat\Behat\Output\Node\EventListener\AST\ScenarioNodeListener;
use Behat\Behat\Output\Node\EventListener\AST\StepListener;
use Behat\Behat\Output\Node\EventListener\AST\SuiteListener;
use Behat\Behat\Output\Node\EventListener\Flow\FireOnlySiblingsListener;
use Behat\Behat\Output\Node\EventListener\Flow\FirstBackgroundFiresFirstListener;
use Behat\Behat\Output\Node\EventListener\Flow\OnlyFirstBackgroundFiresListener;
use Behat\Behat\Output\Printer\ConsoleOutputFactory;
use Behat\Testwork\Output\Node\EventListener\ChainEventListener;
use Behat\Testwork\Output\NodeEventListeningFormatter;
use Behat\Testwork\Output\Printer\StreamOutputPrinter;
use Behat\Testwork\Output\ServiceContainer\Formatter\FormatterFactory;
use Behat\Testwork\Output\ServiceContainer\OutputExtension;
use Digitalnoise\Behat\AsciiDocFormatter\Printer\AsciiDocExampleRowPrinter;
use Digitalnoise\Behat\AsciiDocFormatter\Printer\AsciiDocFeaturePrinter;
use Digitalnoise\Behat\AsciiDocFormatter\Printer\AsciiDocOutlineTablePrinter;
use Digitalnoise\Behat\AsciiDocFormatter\Printer\AsciiDocScenarioPrinter;
use Digitalnoise\Behat\AsciiDocFormatter\Printer\AsciiDocSetupPrinter;
use Digitalnoise\Behat\AsciiDocFormatter\Printer\AsciiDocStepPrinter;
use Digitalnoise\Behat\AsciiDocFormatter\Printer\AsciiDocSuitePrinter;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Philip Weinke <philip.weinke@digitalnoise.de>
 */
class AsciiDocFormatterFactory implements FormatterFactory
{
    public const ROOT_LISTENER_ID = 'asciidoc.node.listener';

    public const SETUP_PRINTER_ID = 'asciidoc.printer.setup';
    public const SUITE_PRINTER_ID = 'asciidoc.printer.suite';
    public const FEATURE_PRINTER_ID = 'asciidoc.printer.feature';
    public const OUTLINE_PRINTER_ID = 'asciidoc.printer.outline';
    public const EXAMPLE_ROW_PRINTER_ID = 'asciidoc.printer.example_row';
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
                $this->rearrangeBackgroundEvents(new Reference(self::ROOT_LISTENER_ID)),
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
                    new Definition(SuiteListener::class, [new Reference(self::SETUP_PRINTER_ID)]),
                    new Definition(
                        FeatureListener::class,
                        [new Reference(self::FEATURE_PRINTER_ID), new Reference(self::SETUP_PRINTER_ID)]
                    ),
                    $this->proxySiblingEvents(
                        BackgroundTested::BEFORE,
                        BackgroundTested::AFTER,
                        [
                            new Definition(
                                ScenarioNodeListener::class,
                                [
                                    BackgroundTested::AFTER_SETUP,
                                    BackgroundTested::AFTER,
                                    new Reference(self::SCENARIO_PRINTER_ID),
                                ]
                            ),
                            new Definition(StepListener::class, [new Reference(self::STEP_PRINTER_ID)]),
                        ]
                    ),
                    $this->proxySiblingEvents(
                        ScenarioTested::BEFORE,
                        ScenarioTested::AFTER,
                        [
                            new Definition(
                                ScenarioNodeListener::class,
                                [
                                    ScenarioTested::AFTER_SETUP,
                                    ScenarioTested::AFTER,
                                    new Reference(self::SCENARIO_PRINTER_ID),
                                ]
                            ),
                            new Definition(StepListener::class, [new Reference(self::STEP_PRINTER_ID)]),
                        ]
                    ),
                    $this->proxySiblingEvents(
                        OutlineTested::BEFORE,
                        OutlineTested::AFTER,
                        [
                            new Definition(
                                OutlineTableListener::class,
                                [
                                    new Reference(self::OUTLINE_PRINTER_ID),
                                    new Reference(self::EXAMPLE_ROW_PRINTER_ID),
                                    new Reference(self::SETUP_PRINTER_ID),
                                    new Reference(self::SETUP_PRINTER_ID),
                                ]
                            ),
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
        $container->setDefinition(self::SETUP_PRINTER_ID, new Definition(AsciiDocSetupPrinter::class));
        $container->setDefinition(self::FEATURE_PRINTER_ID, new Definition(AsciiDocFeaturePrinter::class));
        $container->setDefinition(self::SUITE_PRINTER_ID, new Definition(AsciiDocSuitePrinter::class));
        $container->setDefinition(self::SCENARIO_PRINTER_ID, new Definition(AsciiDocScenarioPrinter::class));
        $container->setDefinition(self::STEP_PRINTER_ID, new Definition(AsciiDocStepPrinter::class));
        $container->setDefinition(self::EXAMPLE_ROW_PRINTER_ID, new Definition(AsciiDocExampleRowPrinter::class));

        $container->setDefinition(
            self::OUTLINE_PRINTER_ID,
            new Definition(
                AsciiDocOutlineTablePrinter::class,
                [new Reference(self::SCENARIO_PRINTER_ID), new Reference(self::STEP_PRINTER_ID)]
            )
        );
    }

    /**
     * @return Definition
     */
    private function createOutputPrinterDefinition(): Definition
    {
        return new Definition(StreamOutputPrinter::class, [new Definition(ConsoleOutputFactory::class)]);
    }

    /**
     * @param Reference $listener
     *
     * @return Definition
     */
    private function rearrangeBackgroundEvents(Reference $listener): Definition
    {
        return new Definition(
            FirstBackgroundFiresFirstListener::class,
            [
                new Definition(OnlyFirstBackgroundFiresListener::class, [$listener]),
            ]
        );
    }

    /**
     * @param ContainerBuilder $container
     */
    public function processFormatter(ContainerBuilder $container)
    {
    }
}
