<?php

namespace VouchedFor\ConsentBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('vouched_for_consent');

        $treeBuilder->getRootNode()
            ->children()
            ->scalarNode('table_name')->end()
            ->scalarNode('password')->end();

        return $treeBuilder;
    }
}
