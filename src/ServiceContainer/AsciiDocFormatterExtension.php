<?php
declare(strict_types=1);

namespace Digitalnoise\Behat\AsciiDocFormatter\ServiceContainer;

use Behat\Testwork\Output\ServiceContainer\OutputExtension;
use Behat\Testwork\ServiceContainer\Extension as BehatExtension;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Digitalnoise\Behat\AsciiDocFormatter\ServiceContainer\Formatter\AsciiDocFormatterFactory;
use RuntimeException;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Philip Weinke <philip.weinke@digitalnoise.de>
 */
class AsciiDocFormatterExtension implements BehatExtension
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
        $extension = $extensionManager->getExtension('formatters');
        if (!$extension instanceof OutputExtension) {
            throw new RuntimeException(
                sprintf(
                    'Expected "%s", got "%s"',
                    ExtensionManager::class,
                    $extension === null ? 'null' : get_class($extension)
                )
            );
        }

        $extension->registerFormatterFactory(new AsciiDocFormatterFactory());
    }

    public function configure(ArrayNodeDefinition $builder)
    {
        $builder->children()->scalarNode('filename')->defaultValue('index.adoc');
        $builder->children()->scalarNode('title')->defaultValue('Living Documentation');
    }

    public function load(ContainerBuilder $container, array $config)
    {
        $container->setParameter('asciidoc.filename', $config['filename']);
        $container->setParameter('asciidoc.title', $config['title']);
    }
}
