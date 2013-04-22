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

use Symfony\Component\HttpFoundation\RedirectResponse;

use CCDNForum\AdminBundle\Controller\CategoryBaseController;

/**
 *
 * @category CCDNForum
 * @package  AdminBundle
 *
 * @author   Reece Fowell <reece@codeconsortium.com>
 * @license  http://opensource.org/licenses/MIT MIT
 * @version  Release: 2.0
 * @link     https://github.com/codeconsortium/CCDNForumAdminBundle
 *
 */
class CategoryController extends CategoryBaseController
{
    /**
     *
     * @access public
     * @return RedirectResponse|RenderResponse
     */
    public function indexAction()
    {
        $this->isAuthorised('ROLE_ADMIN');

        $categories = $this->getCategoryManager()->findAllBoardsGroupedByCategory();

        // Must be consistent with the topics per page on regular user board index.
        $topicsPerPage = $this->getTopicManager()->getTopicsPerPageOnBoards();

        $crumbs = $this->getCrumbs()
            ->add($this->trans('ccdn_forum_admin.crumbs.category.index'), $this->path('ccdn_forum_admin_category_index'));

        return $this->renderResponse('CCDNForumAdminBundle:Category:index.html.', array(
            'crumbs' => $crumbs,
            'categories' => $categories,
            'topics_per_page' => $topicsPerPage,
        ));
    }

    /**
     *
     * @access public
     * @return RedirectResponse|RenderResponse
     */
    public function createAction()
    {
        $this->isAuthorised('ROLE_ADMIN');

        $formHandler = $this->getFormHandlerToCreateCategory();

        if ($formHandler->process($this->getRequest())) {
            $this->setFlash('notice', $this->trans('ccdn_forum_admin.flash.category.create.success'));

            return $this->redirectResponse($this->path('ccdn_forum_admin_category_index'));
        } else {
            // setup crumb trail.
            $crumbs = $this->getCrumbs()
                ->add($this->trans('ccdn_forum_admin.crumbs.category.index'), $this->path('ccdn_forum_admin_category_index'))
                ->add($this->trans('ccdn_forum_admin.crumbs.category.create'), $this->path('ccdn_forum_admin_category_create'));

            return $this->renderResponse('CCDNForumAdminBundle:Category:create.html.', array(
                'crumbs' => $crumbs,
                'form' => $formHandler->getForm()->createView(),
            ));
        }
    }

    /**
     *
     * @access public
     * @param  int                             $categoryId
     * @return RedirectResponse|RenderResponse
     */
    public function editAction($categoryId)
    {
        $this->isAuthorised('ROLE_ADMIN');

        $category = $this->getCategoryManager()->findOneById($categoryId);
        $this->isFound($category);

        $formHandler = $this->getFormHandlerToEditCategory($category);

        if ($formHandler->process($this->getRequest())) {
            $this->setFlash('notice', $this->trans('ccdn_forum_admin.flash.category.edit.success'));

            return $this->redirectResponse($this->path('ccdn_forum_admin_category_index'));
        } else {
            // setup crumb trail.
            $crumbs = $this->getCrumbs()
                ->add($this->trans('ccdn_forum_admin.crumbs.category.index'), $this->path('ccdn_forum_admin_category_index'))
                ->add($this->trans('ccdn_forum_admin.crumbs.category.edit', array('%category_name%' => $category->getName())), $this->path('ccdn_forum_admin_category_edit', array('categoryId' => $categoryId)));

            return $this->renderResponse('CCDNForumAdminBundle:Category:edit.html.', array(
                'category' => $category,
                'crumbs' => $crumbs,
                'form' => $formHandler->getForm()->createView(),
            ));
        }
    }

    /**
     *
     * @access public
     * @param  int                             $categoryId
     * @return RedirectResponse|RenderResponse
     */
    public function deleteAction($categoryId)
    {
        $this->isAuthorised('ROLE_ADMIN');

        $category = $this->getCategoryManager()->findOneById($categoryId);
        $this->isFound($category);

        // setup crumb trail.
        $crumbs = $this->getCrumbs()
            ->add($this->trans('ccdn_forum_admin.crumbs.category.index'), $this->path('ccdn_forum_admin_category_index'))
            ->add($this->trans('ccdn_forum_admin.crumbs.category.delete', array('%category_name%' => $category->getName())), $this->path('ccdn_forum_admin_category_delete', array('categoryId' => $category->getId())));

        return $this->renderResponse('CCDNForumAdminBundle:Category:delete_category.html.', array(
            'category' => $category,
            'crumbs' => $crumbs,
        ));
    }

    /**
     *
     * @access public
     * @param  int              $categoryId
     * @return RedirectResponse
     */
    public function deleteConfirmedAction($categoryId)
    {
        $this->isAuthorised('ROLE_ADMIN');

        $category = $this->getCategoryManager()->findOneById($categoryId);
        $this->isFound($category);

        $this->getCategoryManager()->remove($category)->flush();

        $this->setFlash('notice', $this->trans('ccdn_forum_admin.flash.category.delete.success'));

        return $this->redirectResponse($this->path('ccdn_forum_admin_category_index'));
    }

    /**
     *
     * @access public
     * @param  int              $categoryId, string $direction
     * @return RedirectResponse
     */
    public function reorderAction($categoryId, $direction)
    {
        $this->isAuthorised('ROLE_ADMIN');

        $category = $this->getCategoryManager()->findAllWithBoards();

        if (! $categories) {
            return $this->redirectResponse($this->path('ccdn_forum_admin_category_index'));
        }

        $categoryCount = count($categories);

        // if there is only 1 category, it cannot be re-ordered.
        if ($categoryCount < 2) {
            return $this->redirectResponse($this->path('ccdn_forum_admin_category_index'));
        }

        $this->getCategoryManager()->reorder($categories, $categoryId, $direction)->flush();

        $this->setFlash('notice', $this->trans('ccdn_forum_admin.flash.category.reorder.success'));

        return $this->redirectResponse($this->path('ccdn_forum_admin_category_index'));
    }
}
