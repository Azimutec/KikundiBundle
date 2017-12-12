<?php

namespace Azimutec\KikundiBundle\DependencyInjection;

//use Symfony\Component\DependencyInjection\ContainerBuilder;
//use Symfony\Component\Config\FileLocator;
//use Symfony\Component\HttpKernel\DependencyInjection\Extension;
//use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Yaml\Yaml;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class AzimutecKikundiExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        #$loader->load('config.yml');

        # samuel
        # NON! There is no extension able to load the configuration for "ezpublish" (in /srv/www/ezplatform06/ezplatform/src/Azimutec/KikundiBundle/DependencyInjection/../Resources/config/ezplatform.yml). Looked for namespace "ezpublish", found none
        # $loader->load('ezplatform.yml');

        # Ici on rajoute notre bundle dans la liste des assetic.bundles:
        $aAsseticBundle = $container->getParameter('assetic.bundles');
        $aAsseticBundle[] = 'AzimutecKikundiBundle';
        $container->setParameter('assetic.bundles', $aAsseticBundle);
    }

    /**
     * Allow an extension to prepend the extension configurations.
     * Here we will load our template selection rules.
     *
     * @param ContainerBuilder $container
     */
    public function prepend( ContainerBuilder $container )
    {
        // Loading our YAML file containing our template rules
        $configFile = __DIR__ . '/../Resources/config/ezplatform.yml';
        $config = Yaml::parse( file_get_contents( $configFile ) );
        // We explicitly prepend loaded configuration for "ezpublish" namespace.
        // So it will be placed under the "ezpublish" configuration key, like in ezpublish.yml.
        $container->prependExtensionConfig( 'ezpublish', $config );
        $container->addResource( new FileResource( $configFile ) );


        // Loading our YAML file containing our template rules
        #$config2File = __DIR__ . '/../Resources/config/config.yml';
        #$config2 = Yaml::parse( file_get_contents( $config2File ) );
        // We explicitly prepend loaded configuration for "asssetic" namespace.
        // So it will be placed under the "assetic" configuration key, like in config.yml.
        #$container->prependExtensionConfig( 'assetic', $config2 );
        #$container->addResource( new FileResource( $config2File ) );
    }
}
