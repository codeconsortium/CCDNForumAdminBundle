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

namespace CCDNForum\AdminBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;
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
     * @param Int $categoryId
     * @return RedirectResponse|RenderResponse
     */
    public function createAction($categoryId)
    {
        /*
         *	Invalidate this action / redirect if user should not have access to it
         */
        if ( ! $this->container->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('You do not have permission to access this page!');
        }

        $user = $this->container->get('security.context')->getToken()->getUser();

        $formHandler = $this->container->get('ccdn_forum_admin.board.form.insert.handler')->setDefaultValues(array('category_id' => $categoryId));

        if ($formHandler->process()) {
            $this->container->get('session')->setFlash('notice', $this->container->get('translator')->trans('flash.board.create.success', array(), 'CCDNForumAdminBundle'));

            return new RedirectResponse($this->container->get('router')->generate('ccdn_forum_admin_category_index'));
        } else {
            // setup crumb trail.
            $crumbs = $this->container->get('ccdn_component_crumb.trail')
                ->add($this->container->get('translator')->trans('crumbs.dashboard.admin', array(), 'CCDNForumAdminBundle'), $this->container->get('router')->generate('ccdn_component_dashboard_show', array('category' => 'admin')), "sitemap")
                ->add($this->container->get('translator')->trans('crumbs.category.index', array(), 'CCDNForumAdminBundle'), $this->container->get('router')->generate('ccdn_forum_admin_category_index'), "home")
                ->add($this->container->get('translator')->trans('crumbs.board.create', array(), 'CCDNForumAdminBundle'), $this->container->get('router')->generate('ccdn_forum_admin_board_create'), "edit");

            return $this->container->get('templating')->renderResponse('CCDNForumAdminBundle:Board:create.html.' . $this->getEngine(), array(
                'crumbs' => $crumbs,
                'form' => $formHandler->getForm()->createView(),
            ));
        }
    }

    /**
     *
     * @access public
     * @param Int $boardId
     * @return RedirectResponse|RenderResponse
     */
    public function editAction($boardId)
    {
        /*
         *	Invalidate this action / redirect if user should not have access to it
         */
        if ( ! $this->container->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('You do not have permission to access this page!');
        }

        $user = $this->container->get('security.context')->getToken()->getUser();

        $board = $this->container->get('ccdn_forum_forum.board.repository')->findOneById($boardId);

        if (! $board) {
            throw new NotFoundHTTPException('No such board exists!');
        }

        $formHandler = $this->container->get('ccdn_forum_admin.board.form.update.handler')->setDefaultValues(array('board_entity' => $board));

        if ($formHandler->process()) {
            $this->container->get('session')->setFlash('notice', $this->container->get('translator')->trans('flash.board.edit.success', array(), 'CCDNForumAdminBundle'));

            return new RedirectResponse($this->container->get('router')->generate('ccdn_forum_admin_category_index'));
        } else {
            // setup crumb trail.
            $crumbs = $this->container->get('ccdn_component_crumb.trail')
                ->add($this->container->get('translator')->trans('crumbs.dashboard.admin', array(), 'CCDNForumAdminBundle'), $this->container->get('router')->generate('ccdn_component_dashboard_show', array('category' => 'admin')), "sitemap")
                ->add($this->container->get('translator')->trans('crumbs.category.index', array(), 'CCDNForumAdminBundle'), $this->container->get('router')->generate('ccdn_forum_admin_category_index'), "home")
                ->add($this->container->get('translator')->trans('crumbs.board.edit', array('%board_name%' => $board->getName()), 'CCDNForumAdminBundle'), $this->container->get('router')->generate('ccdn_forum_admin_board_edit', array('boardId' => $boardId)), "edit");

            return $this->container->get('templating')->renderResponse('CCDNForumAdminBundle:Board:edit.html.' . $this->getEngine(), array(
                'board' => $board,
                'crumbs' => $crumbs,
                'form' => $formHandler->getForm()->createView(),
            ));
        }
    }

    /**
     *
     * @access public
     * @param Int $boardId
     * @return RedirectResponse|RenderResponse
     */
    public function deleteAction($boardId)
    {
        if ( ! $this->container->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('You do not have permission to access this page!');
        }

        $board = $this->container->get('ccdn_forum_forum.board.repository')->findOneById($boardId);

        if (! $board) {
            throw new NotFoundHTTPException('No such board exists!');
        }

        // setup crumb trail.
        $crumbs = $this->container->get('ccdn_component_crumb.trail')
            ->add($this->container->get('translator')->trans('crumbs.dashboard.admin', array(), 'CCDNForumAdminBundle'), $this->container->get('router')->generate('ccdn_component_dashboard_show', array('category' => 'admin')), "sitemap")
            ->add($this->container->get('translator')->trans('crumbs.category.index', array(), 'CCDNForumAdminBundle'), $this->container->get('router')->generate('ccdn_forum_admin_category_index'), "home")
            ->add($this->container->get('translator')->trans('crumbs.board.delete', array('%board_name%' => $board->getName()), 'CCDNForumAdminBundle'), $this->container->get('router')->generate('ccdn_forum_admin_board_delete', array('boardId' => $board->getId())), "trash");

        return $this->container->get('templating')->renderResponse('CCDNForumAdminBundle:Board:delete_board.html.' . $this->getEngine(), array(
            'board' => $board,
            'crumbs' => $crumbs,
        ));
    }

    /**
     *
     * @access public
     * @param Int $boardId
     * @return RedirectResponse
     */
    public function deleteConfirmedAction($boardId)
    {
        if ( ! $this->container->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('You do not have permission to access this page!');
        }

        $board = $this->container->get('ccdn_forum_forum.board.repository')->findOneById($boardId);

        if (! $board) {
            throw new NotFoundHTTPException('No such board exists!');
        }

        $this->container->get('ccdn_forum_admin.board.manager')->remove($board)->flush();

        $this->container->get('session')->setFlash('notice', $this->container->get('translator')->trans('flash.board.delete.success', array(), 'CCDNForumAdminBundle'));

        return new RedirectResponse($this->container->get('router')->generate('ccdn_forum_admin_category_index'));
    }

    /**
     *
     * @access public
     * @param Int $boardId, Int $direction
     * @return RedirectResponse
     */
    public function reorderAction($boardId, $direction)
    {
        if ( ! $this->container->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('You do not have permission to access this page!');
        }

        $categoryId = $this->container->get('ccdn_forum_forum.board.repository')->findOneById($boardId)->getCategory()->getId();
        $boards = $this->container->get('ccdn_forum_forum.board.repository')->findBoardsOrderedByPriorityInCategory($categoryId);

        if (! $boards) {
            return new RedirectResponse($this->container->get('router')->generate('ccdn_forum_admin_category_index'));
        }

        $boardCount = count($boards);

        // if there is only 1 category, it cannot be re-ordered.
        if ($boardCount < 2) {
            return new RedirectResponse($this->container->get('router')->generate('ccdn_forum_admin_category_index'));
        }

        $this->container->get('ccdn_forum_admin.board.manager')->reorder($boards, $boardId, $direction)->flush();

        $this->container->get('session')->setFlash('notice', $this->container->get('translator')->trans('flash.board.reorder.success', array(), 'CCDNForumAdminBundle'));

        return new RedirectResponse($this->container->get('router')->generate('ccdn_forum_admin_category_index'));
    }

    /**
     *
     * @access protected
     * @return String
     */
    protected function getEngine()
    {
        return $this->container->getParameter('ccdn_forum_admin.template.engine');
    }

}
