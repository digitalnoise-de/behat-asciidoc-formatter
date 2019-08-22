<?php
declare(strict_types=1);

namespace Digitalnoise\BehatAsciiDocFormatter;

use Behat\Behat\Output\Printer\ConsoleOutputFactory;
use Behat\Testwork\Output\NodeEventListeningFormatter;
use Behat\Testwork\Output\Printer\StreamOutputPrinter;
use Behat\Testwork\Output\ServiceContainer\OutputExtension;
use Behat\Testwork\ServiceContainer\Extension as BehatExtension;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Digitalnoise\BehatAsciiDocFormatter\EventListener\AsciiDocEventListener;
use Digitalnoise\BehatAsciiDocFormatter\Printer\AsciiDocFeaturePrinter;
use Digitalnoise\BehatAsciiDocFormatter\Printer\AsciiDocScenarioPrinter;
use Digitalnoise\BehatAsciiDocFormatter\Printer\AsciiDocStepPrinter;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Class Extension
 *
 * @author Philip Weinke <philip.weinke@digitalnoise.de>
 */
class Extension implements BehatExtension
{
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
        $container->setDefinition('asciidoc.printer.feature', new Definition(AsciiDocFeaturePrinter::class));

        $definition = new Definition(
            NodeEventListeningFormatter::class,
            [
                'asciidoc',
                'Outputs in asciidoc.',
                [],
                $this->createOutputPrinterDefinition(),
                new Definition(
                    AsciiDocEventListener::class,
                    [
                        new Definition(AsciiDocFeaturePrinter::class),
                        new Definition(AsciiDocScenarioPrinter::class),
                        new Definition(AsciiDocStepPrinter::class),
                    ]
                ),
            ]
        );

        $definition->addTag(OutputExtension::FORMATTER_TAG, ['priority' => 100]);
        $container->setDefinition(OutputExtension::FORMATTER_TAG . '.asciidoc', $definition);
    }

    private function createOutputPrinterDefinition()
    {
        return new Definition(StreamOutputPrinter::class, [new Definition(ConsoleOutputFactory::class)]);
    }
}
