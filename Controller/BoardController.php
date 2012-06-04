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
class BoardController extends ContainerAware
{
    
	
	/**
	 *
	 * @access public
	 * @param $category_id
	 * @return RedirectResponse|RenderResponse
	 */
	public function createAction($category_id)
	{
		/*
		 *	Invalidate this action / redirect if user should not have access to it
		 */
		if ( ! $this->container->get('security.context')->isGranted('ROLE_ADMIN')) {
			throw new AccessDeniedException('You do not have permission to access this page!');
		}
		
		$user = $this->container->get('security.context')->getToken()->getUser();
		
		$formHandler = $this->container->get('ccdn_forum_admin.board.form.insert.handler')->setDefaultValues(array('category_id' => $category_id));
		
		if ($formHandler->process())	
		{
			$this->container->get('session')->setFlash('notice', $this->container->get('translator')->trans('flash.board.create.success', array(), 'CCDNForumAdminBundle'));
										
			return new RedirectResponse($this->container->get('router')->generate('cc_admin_forum_category_index'));
		}
		else
		{
			// setup crumb trail.
			$crumb_trail = $this->container->get('ccdn_component_crumb.trail')
				->add($this->container->get('translator')->trans('crumbs.dashboard.admin', array(), 'CCDNForumAdminBundle'), $this->container->get('router')->generate('cc_dashboard_show', array('category' => 'admin')), "sitemap")
				->add($this->container->get('translator')->trans('crumbs.category.index', array(), 'CCDNForumAdminBundle'), $this->container->get('router')->generate('cc_admin_forum_category_index'), "home")
				->add($this->container->get('translator')->trans('crumbs.board.create', array(), 'CCDNForumAdminBundle'), $this->container->get('router')->generate('cc_admin_forum_board_create'), "edit");
					
			return $this->container->get('templating')->renderResponse('CCDNForumAdminBundle:Board:create.html.' . $this->getEngine(), array(
				'crumbs' => $crumb_trail,
				'form' => $formHandler->getForm()->createView(),
			));
		}
	}
	
	
	/**
	 *
	 * @access public
	 * @param $board_id
	 * @return RedirectResponse|RenderResponse
	 */
	public function editAction($board_id)
	{
		/*
		 *	Invalidate this action / redirect if user should not have access to it
		 */
		if ( ! $this->container->get('security.context')->isGranted('ROLE_ADMIN')) {
			throw new AccessDeniedException('You do not have permission to access this page!');
		}
		
		$user = $this->container->get('security.context')->getToken()->getUser();
		
		$board = $this->container->get('ccdn_forum_forum.board.repository')->findOneById($board_id);

		if ( ! $board)
		{
			throw new NotFoundHTTPException('No such board exists!');
		}
		
		$formHandler = $this->container->get('ccdn_forum_admin.board.form.update.handler')->setDefaultValues(array('board_entity' => $board));
				
		if ($formHandler->process())	
		{	
			$this->container->get('session')->setFlash('notice', $this->container->get('translator')->trans('flash.board.edit.success', array(), 'CCDNForumAdminBundle'));
										
			return new RedirectResponse($this->container->get('router')->generate('cc_admin_forum_category_index'));
		}
		else
		{
			// setup crumb trail.
			$crumb_trail = $this->container->get('ccdn_component_crumb.trail')
				->add($this->container->get('translator')->trans('crumbs.dashboard.admin', array(), 'CCDNForumAdminBundle'), $this->container->get('router')->generate('cc_dashboard_show', array('category' => 'admin')), "sitemap")
				->add($this->container->get('translator')->trans('crumbs.category.index', array(), 'CCDNForumAdminBundle'), $this->container->get('router')->generate('cc_admin_forum_category_index'), "home")
				->add($this->container->get('translator')->trans('crumbs.board.edit', array('%board_name%' => $board->getName()), 'CCDNForumAdminBundle'), $this->container->get('router')->generate('cc_admin_forum_board_edit', array('board_id' => $board_id)), "edit");
					
			return $this->container->get('templating')->renderResponse('CCDNForumAdminBundle:Board:edit.html.' . $this->getEngine(), array(
				'board' => $board,
				'crumbs' => $crumb_trail,
				'form' => $formHandler->getForm()->createView(),
			));
		}
	}
	
	
	/**
	 *
	 * @access public
	 * @param $board_id
	 * @return RedirectResponse|RenderResponse
	 */
	public function deleteAction($board_id)
	{
		if ( ! $this->container->get('security.context')->isGranted('ROLE_ADMIN')) {
			throw new AccessDeniedException('You do not have permission to access this page!');
		}
			
		$board = $this->container->get('ccdn_forum_forum.board.repository')->findOneById($board_id);

		if ( ! $board)
		{
			throw new NotFoundHTTPException('No such board exists!');
		}
		
		// setup crumb trail.
		$crumb_trail = $this->container->get('ccdn_component_crumb.trail')
			->add($this->container->get('translator')->trans('crumbs.dashboard.admin', array(), 'CCDNForumAdminBundle'), $this->container->get('router')->generate('cc_dashboard_show', array('category' => 'admin')), "sitemap")
			->add($this->container->get('translator')->trans('crumbs.category.index', array(), 'CCDNForumAdminBundle'), $this->container->get('router')->generate('cc_admin_forum_category_index'), "home")
			->add($this->container->get('translator')->trans('crumbs.board.delete', array('%board_name%' => $board->getName()), 'CCDNForumAdminBundle'), $this->container->get('router')->generate('cc_admin_forum_board_delete', array('board_id' => $board->getId())), "trash");
		
		return $this->container->get('templating')->renderResponse('CCDNForumAdminBundle:Board:delete_board.html.' . $this->getEngine(), array(
			'board' => $board,
			'crumbs' => $crumb_trail,
		));	
	}
	
	
	/**
	 *
	 * @access public
	 * @param $board_id
	 * @return RedirectResponse|RenderResponse
	 */
	public function deleteConfirmedAction($board_id)
	{
		if ( ! $this->container->get('security.context')->isGranted('ROLE_ADMIN')) {
			throw new AccessDeniedException('You do not have permission to access this page!');
		}
		
		$board = $this->container->get('ccdn_forum_forum.board.repository')->findOneById($board_id);
		
		if ( ! $board)
		{
			throw new NotFoundHTTPException('No such board exists!');
		}

		$this->container->get('ccdn_forum_admin.board.manager')->remove($board)->flushNow();
		
		$this->container->get('session')->setFlash('notice', $this->container->get('translator')->trans('flash.board.delete.success', array(), 'CCDNForumAdminBundle'));
			
		return new RedirectResponse($this->container->get('router')->generate('cc_admin_forum_category_index'));
	}
	
	
	/**
	 *
	 * @access public
	 * @param $board_id, $direction
	 * @return RedirectResponse
	 */
	public function reorderAction($board_id, $direction)
	{
		if ( ! $this->container->get('security.context')->isGranted('ROLE_ADMIN')) {
			throw new AccessDeniedException('You do not have permission to access this page!');
		}
		
		$category_id = $this->container->get('ccdn_forum_forum.board.repository')->findOneById($board_id)->getCategory()->getId();
		$boards = $this->container->get('ccdn_forum_forum.board.repository')->findBoardsOrderedByPriorityInCategory($category_id);

		if ( ! $boards)
		{
			return new RedirectResponse($this->container->get('router')->generate('cc_admin_forum_category_index'));
		}

		$board_count = count($boards);

		// if there is only 1 category, it cannot be re-ordered.
		if ($board_count < 2) {
			return new RedirectResponse($this->container->get('router')->generate('cc_admin_forum_category_index'));
		}
		
		$this->container->get('ccdn_forum_admin.board.manager')->reorder($boards, $board_id, $direction)->flushNow();
		
		$this->container->get('session')->setFlash('notice', $this->container->get('translator')->trans('flash.board.reorder.success', array(), 'CCDNForumAdminBundle'));
				
		return new RedirectResponse($this->container->get('router')->generate('cc_admin_forum_category_index'));
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
