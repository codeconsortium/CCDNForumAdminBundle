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

namespace CCDNForum\AdminBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 *
 * @author Reece Fowell <reece@codeconsortium.com>
 * @version 1.0
 */
class CCDNForumAdminBundle extends Bundle
{
	
	/**
	 *
	 * @access public
	 */
	public function boot()
	{
		$twig = $this->container->get('twig');	
		$twig->addGlobal('ccdn_forum_admin', array(
			'seo' => array(
				'title_length' => $this->container->getParameter('ccdn_forum_admin.seo.title_length'),
			),
			'category' => array(
				'index' => array(
					'layout_template' => $this->container->getParameter('ccdn_forum_admin.category.index.layout_template'),
					'enable_bb_parser' => $this->container->getParameter('ccdn_forum_admin.category.index.enable_bb_parser'),
					'last_post_datetime_format' => $this->container->getParameter('ccdn_forum_admin.category.index.last_post_datetime_format'),
				),
				'create' => array(
					'layout_template' => $this->container->getParameter('ccdn_forum_admin.category.create.layout_template'),
					'form_theme' => $this->container->getParameter('ccdn_forum_admin.category.create.form_theme'),
				),
				'edit' => array(
					'layout_template' => $this->container->getParameter('ccdn_forum_admin.category.edit.layout_template'),
					'form_theme' => $this->container->getParameter('ccdn_forum_admin.category.edit.form_theme'),
				),
				'delete' => array(
					'layout_template' => $this->container->getParameter('ccdn_forum_admin.category.delete.layout_template'),
				),
			),
			'board' => array(
				'create' => array(
					'layout_template' => $this->container->getParameter('ccdn_forum_admin.board.create.layout_template'),
					'form_theme' => $this->container->getParameter('ccdn_forum_admin.board.create.form_theme'),
					'enable_bb_editor' => $this->container->getParameter('ccdn_forum_admin.board.create.enable_bb_editor'),
				),
				'edit' => array(
					'layout_template' => $this->container->getParameter('ccdn_forum_admin.board.edit.layout_template'),
					'form_theme' => $this->container->getParameter('ccdn_forum_admin.board.edit.form_theme'),
					'enable_bb_editor' => $this->container->getParameter('ccdn_forum_admin.board.edit.enable_bb_editor'),
				),
				'delete' => array(
					'layout_template' => $this->container->getParameter('ccdn_forum_admin.board.delete.layout_template'),
				),
			),
			'topic' => array(
				'change_board' => array(
					'layout_template' => $this->container->getParameter('ccdn_forum_admin.topic.change_board.layout_template'),
					'form_theme' => $this->container->getParameter('ccdn_forum_admin.topic.change_board.form_theme'),
				),
				'delete_topic' => array(
					'layout_template' => $this->container->getParameter('ccdn_forum_admin.topic.delete_topic.layout_template'),
				),
				'show_closed' => array(
					'layout_template' => $this->container->getParameter('ccdn_forum_admin.topic.show_closed.layout_template'),
					'topic_title_truncate' => $this->container->getParameter('ccdn_forum_admin.topic.show_closed.topic_title_truncate'),
					'post_created_datetime_format' => $this->container->getParameter('ccdn_forum_admin.topic.show_closed.post_created_datetime_format'),
					'topic_closed_datetime_format' => $this->container->getParameter('ccdn_forum_admin.topic.show_closed.topic_closed_datetime_format'),
					'topic_deleted_datetime_format' => $this->container->getParameter('ccdn_forum_admin.topic.show_closed.topic_deleted_datetime_format'),
				),
				'show_deleted' => array(
					'layout_template' => $this->container->getParameter('ccdn_forum_admin.topic.show_deleted.layout_template'),
					'topic_title_truncate' => $this->container->getParameter('ccdn_forum_admin.topic.show_deleted.topic_title_truncate'),
					'topic_closed_datetime_format' => $this->container->getParameter('ccdn_forum_admin.topic.show_deleted.topic_closed_datetime_format'),
					'topic_deleted_datetime_format' => $this->container->getParameter('ccdn_forum_admin.topic.show_deleted.topic_deleted_datetime_format'),
				),
			),
			'post' =>  array(
				'show_deleted' => array(
					'layout_template' => $this->container->getParameter('ccdn_forum_admin.post.show_deleted.layout_template'),
					'topic_title_truncate' => $this->container->getParameter('ccdn_forum_admin.post.show_deleted.topic_title_truncate'),
					'post_created_datetime_format' => $this->container->getParameter('ccdn_forum_admin.post.show_deleted.post_created_datetime_format'),
					'post_locked_datetime_format' => $this->container->getParameter('ccdn_forum_admin.post.show_deleted.post_locked_datetime_format'),
					'post_deleted_datetime_format' => $this->container->getParameter('ccdn_forum_admin.post.show_deleted.post_deleted_datetime_format'),
				),
				'show_locked' => array(
					'layout_template' => $this->container->getParameter('ccdn_forum_admin.post.show_locked.layout_template'),
					'topic_title_truncate' => $this->container->getParameter('ccdn_forum_admin.post.show_locked.topic_title_truncate'),
					'post_created_datetime_format' => $this->container->getParameter('ccdn_forum_admin.post.show_locked.post_created_datetime_format'),
					'post_locked_datetime_format' => $this->container->getParameter('ccdn_forum_admin.post.show_locked.post_locked_datetime_format'),
					'post_deleted_datetime_format' => $this->container->getParameter('ccdn_forum_admin.post.show_locked.post_deleted_datetime_format'),
				),
			),
		));
	}
	
}
