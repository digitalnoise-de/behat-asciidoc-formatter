<?php
declare(strict_types=1);

namespace Digitalnoise\BehatAsciiDocFormatter;

use Behat\Testwork\ServiceContainer\Extension;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class BehatAsciiDocFormatterExtension implements Extension
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
    }
}
