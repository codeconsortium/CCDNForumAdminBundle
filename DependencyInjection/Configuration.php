<?php

/*
 * This file is part of the CCDNForum AdminBundle
 *
 * (c) CCDN (c) CodeConsortium <http://www.codeconsortium.com/>
 *
 * Available on github <http://www.github.com/codeconsortium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CCDNForum\AdminBundle\DependencyInjection;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 *
 * @author Reece Fowell <reece@codeconsortium.com>
 * @version 1.0
 */
class Configuration implements ConfigurationInterface
{

    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('ccdn_forum_admin');

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.
        $rootNode
            ->children()
                ->arrayNode('template')
			        ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('engine')->defaultValue('twig')->end()
                    ->end()
                ->end()
            ->end();

        $this->addSEOSection($rootNode);
        $this->addCategorySection($rootNode);
        $this->addBoardSection($rootNode);
        $this->addTopicSection($rootNode);
        $this->addPostSection($rootNode);

        return $treeBuilder;
    }

    /**
     *
     * @access protected
     * @param ArrayNodeDefinition $node
     */
    protected function addSEOSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('seo')
                    ->addDefaultsIfNotSet()
                    ->canBeUnset()
                    ->children()
                        ->scalarNode('title_length')->defaultValue('67')->end()
                    ->end()
                ->end()
            ->end();
    }

    /**
     *
     * @access private
     * @param ArrayNodeDefinition $node
     */
    private function addCategorySection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('category')
                    ->addDefaultsIfNotSet()
                    ->canBeUnset()
                    ->children()
                        ->arrayNode('index')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('layout_template')->defaultValue('CCDNComponentCommonBundle:Layout:layout_body_right.html.twig')->end()
                                ->scalarNode('last_post_datetime_format')->defaultValue('d-m-Y - H:i')->end()
                                ->scalarNode('enable_bb_parser')->defaultValue(true)->end()
                            ->end()
                        ->end()
                        ->arrayNode('create')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('layout_template')->defaultValue('CCDNComponentCommonBundle:Layout:layout_body_right.html.twig')->end()
                                ->scalarNode('form_theme')->defaultValue('CCDNForumAdminBundle:Form:fields.html.twig')->end()
                            ->end()
                        ->end()
                        ->arrayNode('edit')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('layout_template')->defaultValue('CCDNComponentCommonBundle:Layout:layout_body_right.html.twig')->end()
                                ->scalarNode('form_theme')->defaultValue('CCDNForumAdminBundle:Form:fields.html.twig')->end()
                            ->end()
                        ->end()
                        ->arrayNode('delete')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('layout_template')->defaultValue('CCDNComponentCommonBundle:Layout:layout_body_right.html.twig')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    /**
     *
     * @access private
     * @param ArrayNodeDefinition $node
     */
    private function addBoardSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('board')
                    ->addDefaultsIfNotSet()
                    ->canBeUnset()
                    ->children()
                        ->arrayNode('create')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('layout_template')->defaultValue('CCDNComponentCommonBundle:Layout:layout_body_right.html.twig')->end()
                                ->scalarNode('form_theme')->defaultValue('CCDNForumAdminBundle:Form:fields.html.twig')->end()
                                ->scalarNode('enable_bb_editor')->defaultValue(true)->end()
                            ->end()
                        ->end()
                        ->arrayNode('edit')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('layout_template')->defaultValue('CCDNComponentCommonBundle:Layout:layout_body_right.html.twig')->end()
                                ->scalarNode('form_theme')->defaultValue('CCDNForumAdminBundle:Form:fields.html.twig')->end()
                                ->scalarNode('enable_bb_editor')->defaultValue(true)->end()
                            ->end()
                        ->end()
                        ->arrayNode('delete')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('layout_template')->defaultValue('CCDNComponentCommonBundle:Layout:layout_body_right.html.twig')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    /**
     *
     * @access private
     * @param ArrayNodeDefinition $node
     */
    private function addTopicSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('topic')
                    ->addDefaultsIfNotSet()
                    ->canBeUnset()
                    ->children()
                        ->arrayNode('show_closed')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('layout_template')->defaultValue('CCDNComponentCommonBundle:Layout:layout_body_right.html.twig')->end()
                                ->scalarNode('topics_per_page')->defaultValue('40')->end()
                                ->scalarNode('topic_title_truncate')->defaultValue('20')->end()
                                ->scalarNode('post_created_datetime_format')->defaultValue('d-m-Y - H:i')->end()
                                ->scalarNode('topic_closed_datetime_format')->defaultValue('d-m-Y - H:i')->end()
                                ->scalarNode('topic_deleted_datetime_format')->defaultValue('d-m-Y - H:i')->end()
                            ->end()
                        ->end()
                        ->arrayNode('show_deleted')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('layout_template')->defaultValue('CCDNComponentCommonBundle:Layout:layout_body_right.html.twig')->end()
                                ->scalarNode('topics_per_page')->defaultValue('40')->end()
                                ->scalarNode('topic_title_truncate')->defaultValue('17')->end()
                                ->scalarNode('topic_created_datetime_format')->defaultValue('d-m-Y - H:i')->end()
                                ->scalarNode('topic_closed_datetime_format')->defaultValue('d-m-Y - H:i')->end()
                                ->scalarNode('topic_deleted_datetime_format')->defaultValue('d-m-Y - H:i')->end()
                            ->end()
                        ->end()
                        ->arrayNode('delete_topic')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('layout_template')->defaultValue('CCDNComponentCommonBundle:Layout:layout_body_right.html.twig')->end()
                            ->end()
                        ->end()
                        ->arrayNode('change_board')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('layout_template')->defaultValue('CCDNComponentCommonBundle:Layout:layout_body_right.html.twig')->end()
                                ->scalarNode('form_theme')->defaultValue('CCDNForumAdminBundle:Form:fields.html.twig')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    /**
     *
     * @access private
     * @param ArrayNodeDefinition $node
     */
    private function addPostSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('post')
                    ->addDefaultsIfNotSet()
                    ->canBeUnset()
                    ->children()
                        ->arrayNode('show_locked')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('posts_per_page')->defaultValue('20')->end()
                                ->scalarNode('topic_title_truncate')->defaultValue('20')->end()
                                ->scalarNode('layout_template')->defaultValue('CCDNComponentCommonBundle:Layout:layout_body_right.html.twig')->end()
                                ->scalarNode('post_created_datetime_format')->defaultValue('d-m-Y - H:i')->end()
                                ->scalarNode('post_locked_datetime_format')->defaultValue('d-m-Y - H:i')->end()
                                ->scalarNode('post_deleted_datetime_format')->defaultValue('d-m-Y - H:i')->end()
                            ->end()
                        ->end()
                        ->arrayNode('show_deleted')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('layout_template')->defaultValue('CCDNComponentCommonBundle:Layout:layout_body_right.html.twig')->end()
                                ->scalarNode('posts_per_page')->defaultValue('40')->end()
                                ->scalarNode('topic_title_truncate')->defaultValue('17')->end()
                                ->scalarNode('post_created_datetime_format')->defaultValue('d-m-Y - H:i')->end()
                                ->scalarNode('post_locked_datetime_format')->defaultValue('d-m-Y - H:i')->end()
                                ->scalarNode('post_deleted_datetime_format')->defaultValue('d-m-Y - H:i')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

}
