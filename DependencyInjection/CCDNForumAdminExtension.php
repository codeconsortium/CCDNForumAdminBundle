<?php

/*
 * This file is part of the CCDN AdminBundle
 *
 * (c) CCDN (c) CodeConsortium <http://www.codeconsortium.com/> 
 * 
 * Available on github <http://www.github.com/codeconsortium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CCDNForum\AdminBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 * 
 * @author Reece Fowell <reece@codeconsortium.com> 
 * @version 1.0
 */
class CCDNForumAdminExtension extends Extension
{
	
	
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

		$container->setParameter('ccdn_forum_admin.template.engine', $config['template']['engine']);
		$container->setParameter('ccdn_forum_admin.user.profile_route', $config['user']['profile_route']);
		
		$this->getCategorySection($container, $config);
		$this->getBoardSection($container, $config);
    }
	
	
    /**
     * {@inheritDoc}
     */
	public function getAlias()
	{
		return 'ccdn_forum_admin';
	}
	
	
	/**
	 *
	 * @access private
	 * @param $container, $config
	 */
	private function getCategorySection($container, $config)
	{
		$container->setParameter('ccdn_forum_admin.category.layout_templates.create', $config['category']['layout_templates']['create']);
		$container->setParameter('ccdn_forum_admin.category.layout_templates.delete_category', $config['category']['layout_templates']['delete_category']);
		$container->setParameter('ccdn_forum_admin.category.layout_templates.edit', $config['category']['layout_templates']['edit']);
		$container->setParameter('ccdn_forum_admin.category.layout_templates.index', $config['category']['layout_templates']['index']);
	}


	/**
	 *
	 * @access private
	 * @param $container, $config
	 */	
	private function getBoardSection($container, $config)
	{
		$container->setParameter('ccdn_forum_admin.board.topics_per_page', $config['board']['topics_per_page']);
		
		$container->setParameter('ccdn_forum_admin.board.layout_templates.create', $config['board']['layout_templates']['create']);
		$container->setParameter('ccdn_forum_admin.board.layout_templates.delete_board', $config['board']['layout_templates']['delete_board']);
		$container->setParameter('ccdn_forum_admin.board.layout_templates.edit', $config['board']['layout_templates']['edit']);
	}
	
}
