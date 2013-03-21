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

use CCDNForum\AdminBundle\Controller\BaseController;

/**
 *
 * @author Reece Fowell <reece@codeconsortium.com>
 * @version 1.0
 */
class CategoryController extends BaseController
{
    /**
     *
     * @access public
     * @return RedirectResponse|RenderResponse
     */
    public function indexAction()
    {
        $this->isAuthorised('ROLE_ADMIN');

        $categories = $this->container->get('ccdn_forum_forum.repository.category')->findAllJoinedToBoard();

        // Must be consistent with the topics per page on regular user board index.
        $topicsPerPage = $this->container->getParameter('ccdn_forum_forum.board.show.topics_per_page');

        $crumbs = $this->getCrumbs()
            ->add($this->trans('ccdn_forum_admin.crumbs.category.index'), $this->path('ccdn_forum_admin_category_index'), "home");

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

        $formHandler = $this->container->get('ccdn_forum_admin.form.handler.category_create');

        if ($formHandler->process()) {
            $this->setFlash('notice', $this->trans('ccdn_forum_admin.flash.category.create.success'));

            return new RedirectResponse($this->path('ccdn_forum_admin_category_index'));
        } else {
            // setup crumb trail.
            $crumbs = $this->getCrumbs()
                ->add($this->trans('ccdn_forum_admin.crumbs.category.index'), $this->path('ccdn_forum_admin_category_index'), "home")
                ->add($this->trans('ccdn_forum_admin.crumbs.category.create'), $this->path('ccdn_forum_admin_category_create'), "edit");

            return $this->renderResponse('CCDNForumAdminBundle:Category:create.html.', array(
                'crumbs' => $crumbs,
                'form' => $formHandler->getForm()->createView(),
            ));
        }
    }

    /**
     *
     * @access public
     * @param int $categoryId
     * @return RedirectResponse|RenderResponse
     */
    public function editAction($categoryId)
    {
        $this->isAuthorised('ROLE_ADMIN');

        $category = $this->container->get('ccdn_forum_forum.repository.category')->findOneById($categoryId);
		$this->isFound($category);
		
        $formHandler = $this->container->get('ccdn_forum_admin.form.handler.category_update')->setDefaultValues(array('category_entity' => $category));

        if ($formHandler->process()) {
            $this->setFlash('notice', $this->trans('ccdn_forum_admin.flash.category.edit.success'));

            return new RedirectResponse($this->path('ccdn_forum_admin_category_index'));
        } else {
            // setup crumb trail.
            $crumbs = $this->getCrumbs()
                ->add($this->trans('ccdn_forum_admin.crumbs.category.index'), $this->path('ccdn_forum_admin_category_index'), "home")
                ->add($this->trans('ccdn_forum_admin.crumbs.category.edit', array('%category_name%' => $category->getName())), $this->path('ccdn_forum_admin_category_edit', array('categoryId' => $categoryId)), "edit");

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
     * @param int $categoryId
     * @return RedirectResponse|RenderResponse
     */
    public function deleteAction($categoryId)
    {
        $this->isAuthorised('ROLE_ADMIN');

        $category = $this->container->get('ccdn_forum_forum.repository.category')->findOneById($categoryId);
		$this->isFound($category);
		
        // setup crumb trail.
        $crumbs = $this->getCrumbs()
            ->add($this->trans('ccdn_forum_admin.crumbs.category.index'), $this->path('ccdn_forum_admin_category_index'), "home")
            ->add($this->trans('ccdn_forum_admin.crumbs.category.delete', array('%category_name%' => $category->getName())), $this->path('ccdn_forum_admin_category_delete', array('categoryId' => $category->getId())), "trash");

        return $this->renderResponse('CCDNForumAdminBundle:Category:delete_category.html.', array(
            'category' => $category,
            'crumbs' => $crumbs,
        ));
    }

    /**
     *
     * @access public
     * @param int $categoryId
     * @return RedirectResponse
     */
    public function deleteConfirmedAction($categoryId)
    {
        $this->isAuthorised('ROLE_ADMIN');

        $category = $this->container->get('ccdn_forum_forum.repository.category')->findOneById($categoryId);
		$this->isFound($category);
		
        $this->container->get('ccdn_forum_admin.manager.category')->remove($category)->flush();

        $this->setFlash('notice', $this->trans('ccdn_forum_admin.flash.category.delete.success'));

        return new RedirectResponse($this->path('ccdn_forum_admin_category_index'));
    }

    /**
     *
     * @access public
     * @param int $categoryId, string $direction
     * @return RedirectResponse
     */
    public function reorderAction($categoryId, $direction)
    {
        $this->isAuthorised('ROLE_ADMIN');

        $categories = $this->container->get('ccdn_forum_forum.repository.category')->findCategoriesOrderedByPriority();

        if (! $categories) {
            return new RedirectResponse($this->path('ccdn_forum_admin_category_index'));
        }

        $categoryCount = count($categories);

        // if there is only 1 category, it cannot be re-ordered.
        if ($categoryCount < 2) {
            return new RedirectResponse($this->path('ccdn_forum_admin_category_index'));
        }

        $this->container->get('ccdn_forum_admin.manager.category')->reorder($categories, $categoryId, $direction)->flush();

        $this->setFlash('notice', $this->trans('ccdn_forum_admin.flash.category.reorder.success'));

        return new RedirectResponse($this->path('ccdn_forum_admin_category_index'));
    }
}