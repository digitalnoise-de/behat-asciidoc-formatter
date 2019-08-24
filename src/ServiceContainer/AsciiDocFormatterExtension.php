<?php
declare(strict_types=1);

namespace Digitalnoise\Behat\AsciiDocFormatter\ServiceContainer;

use Behat\Behat\EventDispatcher\Event\BackgroundTested;
use Behat\Behat\EventDispatcher\Event\OutlineTested;
use Behat\Behat\EventDispatcher\Event\ScenarioTested;
use Behat\Behat\Output\Node\EventListener\AST\FeatureListener;
use Behat\Behat\Output\Node\EventListener\AST\OutlineTableListener;
use Behat\Behat\Output\Node\EventListener\AST\ScenarioNodeListener;
use Behat\Behat\Output\Node\EventListener\AST\StepListener;
use Behat\Behat\Output\Node\EventListener\AST\SuiteListener;
use Behat\Behat\Output\Node\EventListener\Flow\FireOnlySiblingsListener;
use Behat\Behat\Output\Printer\ConsoleOutputFactory;
use Behat\Testwork\Output\Node\EventListener\ChainEventListener;
use Behat\Testwork\Output\NodeEventListeningFormatter;
use Behat\Testwork\Output\Printer\StreamOutputPrinter;
use Behat\Testwork\Output\ServiceContainer\OutputExtension;
use Behat\Testwork\ServiceContainer\Extension as BehatExtension;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Digitalnoise\Behat\AsciiDocFormatter\Printer\AsciiDocExampleRowPrinter;
use Digitalnoise\Behat\AsciiDocFormatter\Printer\AsciiDocFeaturePrinter;
use Digitalnoise\Behat\AsciiDocFormatter\Printer\AsciiDocOutlineTablePrinter;
use Digitalnoise\Behat\AsciiDocFormatter\Printer\AsciiDocScenarioPrinter;
use Digitalnoise\Behat\AsciiDocFormatter\Printer\AsciiDocSetupPrinter;
use Digitalnoise\Behat\AsciiDocFormatter\Printer\AsciiDocStepPrinter;
use Digitalnoise\Behat\AsciiDocFormatter\Printer\AsciiDocSuitePrinter;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Philip Weinke <philip.weinke@digitalnoise.de>
 */
class AsciiDocFormatterExtension implements BehatExtension
{
    private const SETUP_PRINTER = 'asciidoc.printer.setup';
    private const SUITE_PRINTER = 'asciidoc.printer.suite';
    private const FEATURE_PRINTER = 'asciidoc.printer.feature';
    private const OUTLINE_PRINTER = 'asciidoc.printer.outline';
    private const EXAMPLE_ROW_PRINTER = 'asciidoc.printer.example_row';
    private const SCENARIO_PRINTER = 'asciidoc.printer.scenario';
    private const STEP_PRINTER = 'asciidoc.printer.step';

    public function process(ContainerBuilder $container)
    {
    }

    public function getConfigKey()
    {
        return 'asciidoc';
    }

    public function initialize(ExtensionManager $extensionManager)
    {
    }

    public function configure(ArrayNodeDefinition $builder)
    {
    }

    public function load(ContainerBuilder $container, array $config)
    {
        $this->loadPrinter($container);

        $chainEventListener = new Definition(
            ChainEventListener::class,
            [
                [
                    new Definition(SuiteListener::class, [new Reference(self::SETUP_PRINTER)]),
                    new Definition(
                        FeatureListener::class,
                        [new Reference(self::FEATURE_PRINTER), new Reference(self::SETUP_PRINTER)]
                    ),
                    new Definition(
                        ScenarioNodeListener::class,
                        [
                            BackgroundTested::AFTER_SETUP,
                            BackgroundTested::AFTER,
                            new Reference(self::SCENARIO_PRINTER),
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
                                    new Reference(self::SCENARIO_PRINTER),
                                ]
                            ),
                            new Definition(StepListener::class, [new Reference(self::STEP_PRINTER)]),
                        ]
                    ),
                    $this->proxySiblingEvents(
                        OutlineTested::BEFORE,
                        OutlineTested::AFTER,
                        [
                            new Definition(
                                OutlineTableListener::class,
                                [
                                    new Reference(self::OUTLINE_PRINTER),
                                    new Reference(self::EXAMPLE_ROW_PRINTER),
                                    new Reference(self::SETUP_PRINTER),
                                    new Reference(self::SETUP_PRINTER),
                                ]
                            ),
                        ]
                    ),
                ],
            ]
        );

        $definition = new Definition(
            NodeEventListeningFormatter::class,
            [
                'asciidoc',
                'Outputs in asciidoc.',
                [],
                $this->createOutputPrinterDefinition(),
                $chainEventListener,
            ]
        );

        $definition->addTag(OutputExtension::FORMATTER_TAG, ['priority' => 100]);
        $container->setDefinition(OutputExtension::FORMATTER_TAG . '.asciidoc', $definition);
    }

    private function loadPrinter(ContainerBuilder $container): void
    {
        $container->setDefinition(self::SETUP_PRINTER, new Definition(AsciiDocSetupPrinter::class));
        $container->setDefinition(self::FEATURE_PRINTER, new Definition(AsciiDocFeaturePrinter::class));
        $container->setDefinition(self::SUITE_PRINTER, new Definition(AsciiDocSuitePrinter::class));
        $container->setDefinition(self::SCENARIO_PRINTER, new Definition(AsciiDocScenarioPrinter::class));
        $container->setDefinition(self::STEP_PRINTER, new Definition(AsciiDocStepPrinter::class));
        $container->setDefinition(self::EXAMPLE_ROW_PRINTER, new Definition(AsciiDocExampleRowPrinter::class));

        $container->setDefinition(
            self::OUTLINE_PRINTER,
            new Definition(
                AsciiDocOutlineTablePrinter::class,
                [new Reference(self::SCENARIO_PRINTER), new Reference(self::STEP_PRINTER)]
            )
        );
    }

    /**
     * @param string       $beforeEventName
     * @param string       $afterEventName
     * @param Definition[] $listeners
     *
     * @return Definition
     */
    protected function proxySiblingEvents(string $beforeEventName, string $afterEventName, array $listeners): Definition
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

    private function createOutputPrinterDefinition()
    {
        return new Definition(StreamOutputPrinter::class, [new Definition(ConsoleOutputFactory::class)]);
    }
}
