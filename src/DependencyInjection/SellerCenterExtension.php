<?php

namespace SellerCenter\DependencyInjection;


use Exception;
use InvalidArgumentException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class SellerCenterExtension extends Extension
{
    /**
     * Loads a specific configuration.
     *
     * @throws InvalidArgumentException When provided tag is not defined in this extension
     * @throws Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(dirname(__DIR__).'/../Resources/config')
        );
        $loader->load('services.yaml');
    }
}