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

use CCDNForum\AdminBundle\Controller\BoardBaseController;

/**
 *
 * @author Reece Fowell <reece@codeconsortium.com>
 * @version 1.0
 */
class BoardController extends BoardBaseController
{
    /**
     *
     * @access public
     * @param int $categoryId
     * @return RedirectResponse|RenderResponse
     */
    public function createAction($categoryId)
    {
        $this->isAuthorised('ROLE_ADMIN');

		$category = $this->getCategoryManager()->findOneById($categoryId);
		$this->isFound($category);

        $formHandler = $this->getFormHandlerToCreateBoard($category);

        if ($formHandler->process($this->getRequest())) {
            $this->setFlash('notice', $this->trans('ccdn_forum_admin.flash.board.create.success'));

            return new RedirectResponse($this->path('ccdn_forum_admin_category_index'));
        } else {
            // setup crumb trail.
            $crumbs = $this->getCrumbs()
                ->add($this->trans('ccdn_forum_admin.crumbs.category.index'), $this->path('ccdn_forum_admin_category_index'), "home")
                ->add($this->trans('ccdn_forum_admin.crumbs.board.create'), $this->path('ccdn_forum_admin_board_create'), "edit");

            return $this->renderResponse('CCDNForumAdminBundle:Board:create.html.', array(
                'crumbs' => $crumbs,
                'form' => $formHandler->getForm()->createView(),
            ));
        }
    }

    /**
     *
     * @access public
     * @param int $boardId
     * @return RedirectResponse|RenderResponse
     */
    public function editAction($boardId)
    {
        $this->isAuthorised('ROLE_ADMIN');

		$board = $this->getBoardManager()->findOneByIdWithCategory($boardId);
        $this->isFound($board);

        $formHandler = $this->getFormHandlerToEditBoard($board);

        if ($formHandler->process($this->getRequest())) {
            $this->setFlash('notice', $this->trans('ccdn_forum_admin.flash.board.edit.success'));

            return new RedirectResponse($this->path('ccdn_forum_admin_category_index'));
        } else {
            // setup crumb trail.
            $crumbs = $this->getCrumbs()
                ->add($this->trans('ccdn_forum_admin.crumbs.category.index'), $this->path('ccdn_forum_admin_category_index'), "home")
                ->add($this->trans('ccdn_forum_admin.crumbs.board.edit', array('%board_name%' => $board->getName())), $this->path('ccdn_forum_admin_board_edit', array('boardId' => $boardId)), "edit");

            return $this->renderResponse('CCDNForumAdminBundle:Board:edit.html.', array(
                'board' => $board,
                'crumbs' => $crumbs,
                'form' => $formHandler->getForm()->createView(),
            ));
        }
    }

    /**
     *
     * @access public
     * @param int $boardId
     * @return RedirectResponse|RenderResponse
     */
    public function deleteAction($boardId)
    {
        $this->isAuthorised('ROLE_ADMIN');

		$board = $this->getBoardManager()->findOneById($boardId);
        $this->isFound($board);

        // setup crumb trail.
        $crumbs = $this->getCrumbs()
            ->add($this->trans('ccdn_forum_admin.crumbs.category.index'), $this->path('ccdn_forum_admin_category_index'), "home")
            ->add($this->trans('ccdn_forum_admin.crumbs.board.delete', array('%board_name%' => $board->getName())), $this->path('ccdn_forum_admin_board_delete', array('boardId' => $board->getId())), "trash");

        return $this->renderResponse('CCDNForumAdminBundle:Board:delete_board.html.', array(
            'board' => $board,
            'crumbs' => $crumbs,
        ));
    }

    /**
     *
     * @access public
     * @param int $boardId
     * @return RedirectResponse
     */
    public function deleteConfirmedAction($boardId)
    {
        $this->isAuthorised('ROLE_ADMIN');

		$board = $this->getBoardManager()->findOneById($boardId);
        $this->isFound($board);

        $this->getBoardManager()->remove($board)->flush();

        $this->setFlash('notice', $this->trans('ccdn_forum_admin.flash.board.delete.success'));

        return new RedirectResponse($this->path('ccdn_forum_admin_category_index'));
    }

    /**
     *
     * @access public
     * @param int $boardId, int $direction
     * @return RedirectResponse
     */
    public function reorderAction($boardId, $direction)
    {
        $this->isAuthorised('ROLE_ADMIN');

		$board = $this->getBoardManager()->findOneByIdWithCategory($boardId);
		$this->isFound($board);
		
		$boards = $this->getCategoryManager()->findOneByIdWithBoards($board->getCategory()->getId());

        if (! $boards) {
            return new RedirectResponse($this->path('ccdn_forum_admin_category_index'));
        }

        // if there is only 1 board, it cannot be re-ordered.
        if (count($boards) < 2) {
            return new RedirectResponse($this->path('ccdn_forum_admin_category_index'));
        }

        $this->getBoardManager()->reorder($boards, $boardId, $direction)->flush();

        $this->setFlash('notice', $this->trans('ccdn_forum_admin.flash.board.reorder.success'));

        return new RedirectResponse($this->path('ccdn_forum_admin_category_index'));
    }
}