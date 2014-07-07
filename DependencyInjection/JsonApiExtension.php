<?php

namespace GrailleLabs\JsonApiBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class JsonApiExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        // Préparations des configs
        $config = array();
        foreach ($configs as $subConfig)
            $config = array_merge($config, $subConfig);

        /*
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        */

        // Importation des services
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        // Gestion de la configuration sémantique
        if (!isset($config['servers']))
            throw new \InvalidArgumentException('JsonAPIBundle - Vous devez spécifier des serveurs de jeu dans app/config/config.yml');

        if (!isset($config['servers']['default']))
            throw new \InvalidArgumentException('JsonAPIBundle - Vous devez spécifier un serveur par defaut dans app/config/config.yml');

        $container->setParameter('glabs.json_api.servers', $config['servers']);
    }
}
