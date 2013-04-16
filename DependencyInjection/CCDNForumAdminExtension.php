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

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Config\FileLocator;

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
	 *
     * @access public
	 * @return string
     */
    public function getAlias()
    {
        return 'ccdn_forum_admin';
    }

    /**
     *
     * @access public
	 * @param array $config
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

		// Class file namespaces.
        $this
			->getEntitySection($config, $container)
	        ->getRepositorySection($config, $container)
	        ->getGatewaySection($config, $container)
	        ->getManagerSection($config, $container)
	        ->getFormSection($config, $container)
			->getComponentSection($config, $container)
		;
			
		// Configuration stuff.
        $container->setParameter('ccdn_forum_admin.template.engine', $config['template']['engine']);

        $this
			->getSEOSection($config, $container)
	        ->getCategorySection($config, $container)
	        ->getBoardSection($config, $container)
	        ->getTopicSection($config, $container)
	        ->getPostSection($config, $container)
		;

		// Load Service definitions.
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }
	
    /**
     *
     * @access private
	 * @param array $config
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
	 * @return \CCDNForum\ForumBundle\DependencyInjection\CCDNForumForumExtension
     */
    private function getEntitySection(array $config, ContainerBuilder $container)
    {
        $container->setParameter('ccdn_forum_admin.entity.category.class', $config['entity']['category']['class']);
        $container->setParameter('ccdn_forum_admin.entity.board.class', $config['entity']['board']['class']);
        $container->setParameter('ccdn_forum_admin.entity.topic.class', $config['entity']['topic']['class']);
        $container->setParameter('ccdn_forum_admin.entity.post.class', $config['entity']['post']['class']);
        $container->setParameter('ccdn_forum_admin.entity.draft.class', $config['entity']['draft']['class']);
        $container->setParameter('ccdn_forum_admin.entity.subscription.class', $config['entity']['subscription']['class']);		
        $container->setParameter('ccdn_forum_admin.entity.registry.class', $config['entity']['registry']['class']);	
		
		return $this;
	}
	
    /**
     *
     * @access private
	 * @param array $config
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
	 * @return \CCDNForum\ForumBundle\DependencyInjection\CCDNForumForumExtension
     */
    private function getRepositorySection(array $config, ContainerBuilder $container)
    {
        $container->setParameter('ccdn_forum_admin.repository.category.class', $config['repository']['category']['class']);
        $container->setParameter('ccdn_forum_admin.repository.board.class', $config['repository']['board']['class']);
        $container->setParameter('ccdn_forum_admin.repository.topic.class', $config['repository']['topic']['class']);
        $container->setParameter('ccdn_forum_admin.repository.post.class', $config['repository']['post']['class']);
        $container->setParameter('ccdn_forum_admin.repository.draft.class', $config['repository']['draft']['class']);
        $container->setParameter('ccdn_forum_admin.repository.subscription.class', $config['repository']['subscription']['class']);		
        $container->setParameter('ccdn_forum_admin.repository.registry.class', $config['repository']['registry']['class']);	
		
		return $this;
	}
	
    /**
     *
     * @access private
	 * @param array $config
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
	 * @return \CCDNForum\ForumBundle\DependencyInjection\CCDNForumForumExtension
     */
    private function getGatewaySection(array $config, ContainerBuilder $container)
    {
        $container->setParameter('ccdn_forum_admin.gateway_bag.class', $config['gateway']['bag']['class']);

        $container->setParameter('ccdn_forum_admin.gateway.category.class', $config['gateway']['category']['class']);
        $container->setParameter('ccdn_forum_admin.gateway.board.class', $config['gateway']['board']['class']);
        $container->setParameter('ccdn_forum_admin.gateway.topic.class', $config['gateway']['topic']['class']);
        $container->setParameter('ccdn_forum_admin.gateway.post.class', $config['gateway']['post']['class']);
        $container->setParameter('ccdn_forum_admin.gateway.draft.class', $config['gateway']['draft']['class']);
        $container->setParameter('ccdn_forum_admin.gateway.subscription.class', $config['gateway']['subscription']['class']);		
        $container->setParameter('ccdn_forum_admin.gateway.registry.class', $config['gateway']['registry']['class']);	
		
		return $this;
	}
	
    /**
     *
     * @access private
	 * @param array $config
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
	 * @return \CCDNForum\ForumBundle\DependencyInjection\CCDNForumForumExtension
     */
    private function getManagerSection(array $config, ContainerBuilder $container)
    {
        $container->setParameter('ccdn_forum_admin.manager_bag.class', $config['manager']['bag']['class']);

        $container->setParameter('ccdn_forum_admin.manager.category.class', $config['manager']['category']['class']);
        $container->setParameter('ccdn_forum_admin.manager.board.class', $config['manager']['board']['class']);
        $container->setParameter('ccdn_forum_admin.manager.topic.class', $config['manager']['topic']['class']);
        $container->setParameter('ccdn_forum_admin.manager.post.class', $config['manager']['post']['class']);
        $container->setParameter('ccdn_forum_admin.manager.draft.class', $config['manager']['draft']['class']);
        $container->setParameter('ccdn_forum_admin.manager.subscription.class', $config['manager']['subscription']['class']);		
        $container->setParameter('ccdn_forum_admin.manager.registry.class', $config['manager']['registry']['class']);		
		
		return $this;
	}
	
    /**
     *
     * @access private
	 * @param array $config
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
	 * @return \CCDNForum\AdminBundle\DependencyInjection\CCDNForumAdminExtension
     */
    private function getFormSection(array $config, ContainerBuilder $container)
    {
        $container->setParameter('ccdn_forum_admin.form.type.category_create.class', $config['form']['type']['category_create']['class']);
        $container->setParameter('ccdn_forum_admin.form.type.category_update.class', $config['form']['type']['category_update']['class']);
        $container->setParameter('ccdn_forum_admin.form.type.board_create.class', $config['form']['type']['board_create']['class']);
        $container->setParameter('ccdn_forum_admin.form.type.board_update.class', $config['form']['type']['board_update']['class']);

        $container->setParameter('ccdn_forum_admin.form.handler.category_create.class', $config['form']['handler']['category_create']['class']);
        $container->setParameter('ccdn_forum_admin.form.handler.category_update.class', $config['form']['handler']['category_update']['class']);
        $container->setParameter('ccdn_forum_admin.form.handler.board_create.class', $config['form']['handler']['board_create']['class']);
        $container->setParameter('ccdn_forum_admin.form.handler.board_update.class', $config['form']['handler']['board_update']['class']);
		
		return $this;
	}

    /**
     *
     * @access private
	 * @param array $config
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
	 * @return \CCDNForum\AdminBundle\DependencyInjection\CCDNForumAdminExtension
     */
    private function getComponentSection(array $config, ContainerBuilder $container)
    {
        $container->setParameter('ccdn_forum_admin.component.dashboard.integrator.class', $config['component']['dashboard']['integrator']['class']);		

		return $this;
	}
	
    /**
     *
     * @access private
	 * @param array $config
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
	 * @return \CCDNForum\AdminBundle\DependencyInjection\CCDNForumAdminExtension
     */
    private function getSEOSection(array $config, ContainerBuilder $container)
    {
        $container->setParameter('ccdn_forum_admin.seo.title_length', $config['seo']['title_length']);
		
		return $this;
    }

    /**
     *
     * @access private
	 * @param array $config
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
	 * @return \CCDNForum\AdminBundle\DependencyInjection\CCDNForumAdminExtension
     */
    private function getCategorySection(array $config, ContainerBuilder $container)
    {
        $container->setParameter('ccdn_forum_admin.category.index.layout_template', $config['category']['index']['layout_template']);
        $container->setParameter('ccdn_forum_admin.category.index.last_post_datetime_format', $config['category']['index']['last_post_datetime_format']);
        $container->setParameter('ccdn_forum_admin.category.index.enable_bb_parser', $config['category']['index']['enable_bb_parser']);

        $container->setParameter('ccdn_forum_admin.category.create.layout_template', $config['category']['create']['layout_template']);
        $container->setParameter('ccdn_forum_admin.category.create.form_theme', $config['category']['create']['form_theme']);

        $container->setParameter('ccdn_forum_admin.category.edit.layout_template', $config['category']['edit']['layout_template']);
        $container->setParameter('ccdn_forum_admin.category.edit.form_theme', $config['category']['edit']['form_theme']);

        $container->setParameter('ccdn_forum_admin.category.delete.layout_template', $config['category']['delete']['layout_template']);
		
		return $this;
    }

    /**
     *
     * @access private
	 * @param array $config
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
	 * @return \CCDNForum\AdminBundle\DependencyInjection\CCDNForumAdminExtension
     */
    private function getBoardSection(array $config, ContainerBuilder $container)
    {
        $container->setParameter('ccdn_forum_admin.board.create.layout_template', $config['board']['create']['layout_template']);
        $container->setParameter('ccdn_forum_admin.board.create.form_theme', $config['board']['create']['form_theme']);
        $container->setParameter('ccdn_forum_admin.board.create.enable_bb_editor', $config['board']['create']['enable_bb_editor']);

        $container->setParameter('ccdn_forum_admin.board.edit.layout_template', $config['board']['edit']['layout_template']);
        $container->setParameter('ccdn_forum_admin.board.edit.form_theme', $config['board']['edit']['form_theme']);
        $container->setParameter('ccdn_forum_admin.board.edit.enable_bb_editor', $config['board']['edit']['enable_bb_editor']);

        $container->setParameter('ccdn_forum_admin.board.delete.layout_template', $config['board']['delete']['layout_template']);
		
		return $this;
    }

    /**
     *
     * @access private
	 * @param array $config
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
	 * @return \CCDNForum\AdminBundle\DependencyInjection\CCDNForumAdminExtension
     */
    private function getTopicSection(array $config, ContainerBuilder $container)
    {
        $container->setParameter('ccdn_forum_admin.topic.show_closed.layout_template', $config['topic']['show_closed']['layout_template']);
        $container->setParameter('ccdn_forum_admin.topic.show_closed.topics_per_page', $config['topic']['show_closed']['topics_per_page']);
        $container->setParameter('ccdn_forum_admin.topic.show_closed.topic_title_truncate', $config['topic']['show_closed']['topic_title_truncate']);
        $container->setParameter('ccdn_forum_admin.topic.show_closed.post_created_datetime_format', $config['topic']['show_closed']['post_created_datetime_format']);
        $container->setParameter('ccdn_forum_admin.topic.show_closed.topic_closed_datetime_format', $config['topic']['show_closed']['topic_closed_datetime_format']);
        $container->setParameter('ccdn_forum_admin.topic.show_closed.topic_deleted_datetime_format', $config['topic']['show_closed']['topic_deleted_datetime_format']);

        $container->setParameter('ccdn_forum_admin.topic.show_deleted.layout_template', $config['topic']['show_deleted']['layout_template']);
        $container->setParameter('ccdn_forum_admin.topic.show_deleted.topics_per_page', $config['topic']['show_deleted']['topics_per_page']);
        $container->setParameter('ccdn_forum_admin.topic.show_deleted.topic_title_truncate', $config['topic']['show_deleted']['topic_title_truncate']);
        $container->setParameter('ccdn_forum_admin.topic.show_deleted.topic_created_datetime_format', $config['topic']['show_deleted']['topic_created_datetime_format']);
        $container->setParameter('ccdn_forum_admin.topic.show_deleted.topic_closed_datetime_format', $config['topic']['show_deleted']['topic_closed_datetime_format']);
        $container->setParameter('ccdn_forum_admin.topic.show_deleted.topic_deleted_datetime_format', $config['topic']['show_deleted']['topic_deleted_datetime_format']);

        $container->setParameter('ccdn_forum_admin.topic.delete_topic.layout_template', $config['topic']['delete_topic']['layout_template']);
		
		return $this;
    }

    /**
     *
     * @access private
	 * @param array $config
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
	 * @return \CCDNForum\AdminBundle\DependencyInjection\CCDNForumAdminExtension
     */
    private function getPostSection(array $config, ContainerBuilder $container)
    {
        $container->setParameter('ccdn_forum_admin.post.show_locked.layout_template', $config['post']['show_locked']['layout_template']);
        $container->setParameter('ccdn_forum_admin.post.show_locked.posts_per_page', $config['post']['show_locked']['posts_per_page']);
        $container->setParameter('ccdn_forum_admin.post.show_locked.topic_title_truncate', $config['post']['show_locked']['topic_title_truncate']);
        $container->setParameter('ccdn_forum_admin.post.show_locked.post_created_datetime_format', $config['post']['show_locked']['post_created_datetime_format']);
        $container->setParameter('ccdn_forum_admin.post.show_locked.post_locked_datetime_format', $config['post']['show_locked']['post_locked_datetime_format']);
        $container->setParameter('ccdn_forum_admin.post.show_locked.post_deleted_datetime_format', $config['post']['show_locked']['post_deleted_datetime_format']);

        $container->setParameter('ccdn_forum_admin.post.show_deleted.layout_template', $config['post']['show_deleted']['layout_template']);
        $container->setParameter('ccdn_forum_admin.post.show_deleted.posts_per_page', $config['post']['show_deleted']['posts_per_page']);
        $container->setParameter('ccdn_forum_admin.post.show_deleted.topic_title_truncate', $config['post']['show_deleted']['topic_title_truncate']);
        $container->setParameter('ccdn_forum_admin.post.show_deleted.post_created_datetime_format', $config['post']['show_deleted']['post_created_datetime_format']);
        $container->setParameter('ccdn_forum_admin.post.show_deleted.post_locked_datetime_format', $config['post']['show_deleted']['post_locked_datetime_format']);
        $container->setParameter('ccdn_forum_admin.post.show_deleted.post_deleted_datetime_format', $config['post']['show_deleted']['post_deleted_datetime_format']);
		
		return $this;
    }
}
