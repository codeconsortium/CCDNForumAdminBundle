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

namespace CCDNForum\AdminBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * 
 * @author Reece Fowell <reece@codeconsortium.com> 
 * @version 1.0
 */
class TopicController extends ContainerAware
{

	
	/**
	 *
	 * Displays a list of soft deleted topics
	 *
	 * @access public
	 * @return RedirectResponse|RenderResponse
	 */
	public function showTrashedAction($page)
	{
		if ( ! $this->container->get('security.context')->isGranted('ROLE_ADMIN'))
		{
			throw new AccessDeniedException('You do not have access to this section.');
		}

		$user = $this->container->get('security.context')->getToken()->getUser();

		$topics_paginated = $this->container->get('ccdn_forum_forum.topic.repository')->findClosedTopicsForModeratorsPaginated();
			
		$topics_per_page = $this->container->getParameter('ccdn_forum_moderator.topic.topics_per_page');
		$topics_paginated->setMaxPerPage($topics_per_page);
		$topics_paginated->setCurrentPage($page, false, true);
		
		$posts_per_page = $this->container->getParameter('ccdn_forum_moderator.topic.posts_per_page');
		
		// setup crumb trail.
		$crumb_trail = $this->container->get('ccdn_component_crumb.trail')
			->add($this->container->get('translator')->trans('crumbs.dashboard', array(), 'CCDNForumAdminBundle'), $this->container->get('router')->generate('cc_dashboard_index'), "sitemap")
			->add($this->container->get('translator')->trans('crumbs.dashboard.admin', array(), 'CCDNForumAdminBundle'), $this->container->get('router')->generate('cc_dashboard_show', array('category' => 'admin')), "sitemap")
			->add($this->container->get('translator')->trans('crumbs.topic.deleted', array(), 'CCDNForumAdminBundle'), $this->container->get('router')->generate('cc_admin_forum_topic_deleted_show'), "trash");
		
		return $this->container->get('templating')->renderResponse('CCDNForumAdminBundle:Topic:show_deleted.html.' . $this->getEngine(), array(
			'user_profile_route' => $this->container->getParameter('ccdn_forum_admin.user.profile_route'),
			'user' => $user,
			'topics' => $topics_paginated,
			'crumbs' => $crumb_trail,
			'pager' => $topics_paginated,
			'posts_per_page' => $posts_per_page,
		));
	}


	/**
	 *
	 * Restores/Deletes items, marked for removal from db via checkboxes for each
	 * item present in a form. This can only be done via a member with role_admin!
	 *
	 * @access public
	 * @return RedirectResponse|RenderResponse
	 */
	public function bulkAction()
	{
		if ( ! $this->container->get('security.context')->isGranted('ROLE_ADMIN'))
		{
			throw new AccessDeniedException('You do not have access to this section.');
		}

		//
		// Get all the checked item id's.
		//
		$itemIds = array();
		$ids = $_POST;
		foreach ($ids as $itemKey => $itemId)
		{
			if (substr($itemKey, 0, 6) == 'check_')
			{
				//
				// Cast the key values to int upon extraction. 
				//
				$id = (int) substr($itemKey, 6, (strlen($itemKey) - 6));

				if (is_int($id) == true)
				{
					$itemIds[] = $id;
				}
			}
		}

		//
		// Don't bother if there are no flags to process.
		//
		if (count($itemIds) < 1)
		{
			return new RedirectResponse($this->container->get('router')->generate('cc_admin_forum_topic_deleted_show'));
		}

		$user = $this->container->get('security.context')->getToken()->getUser();

		$topics = $this->container->get('ccdn_forum_forum.topic.repository')->findTheseTopicsByIdForModeration($itemIds);

		if ( ! $topics || empty($topics))
		{
			$this->container->get('session')->setFlash('notice', $this->container->get('translator')->trans('flash.topic.no_topics_found', array(), 'CCDNForumModeratorBundle'));

			return new RedirectResponse($this->container->get('router')->generate('cc_admin_forum_topic_deleted_show'));
		}
		
		if (isset($_POST['submit_close']))
		{
			$this->container->get('ccdn_forum_forum.topic.manager')->bulkClose($topics)->flushNow();
		}
		if (isset($_POST['submit_reopen']))
		{
			$this->container->get('ccdn_forum_forum.topic.manager')->bulkReopen($topics)->flushNow();
		}
		if (isset($_POST['submit_restore']))
		{
			$this->container->get('ccdn_forum_forum.topic.manager')->bulkRestore($topics)->flushNow();
		}
		if (isset($_POST['submit_delete']))
		{
			$this->container->get('ccdn_forum_forum.topic.manager')->bulkHardDelete($topics)->flushNow();
		}

		return new RedirectResponse($this->container->get('router')->generate('cc_admin_forum_topic_deleted_show'));
	}

	
		
	
	/**
	 *
	 * @access protected
	 * @return string
	 */
	protected function getEngine()
    {
        return $this->container->getParameter('ccdn_forum_admin.template.engine');
    }

}
