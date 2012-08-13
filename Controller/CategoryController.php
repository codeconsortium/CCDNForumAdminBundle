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
class CategoryController extends ContainerAware
{

    /**
     *
     * @access public
     * @return RedirectResponse|RenderResponse
     */
    public function indexAction()
    {
        /*
         *	Invalidate this action / redirect if user should not have access to it
         */
        if ( ! $this->container->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('You do not have permission to access this page!');
        }

        $categories = $this->container->get('ccdn_forum_forum.category.repository')->findAllJoinedToBoard();

        // Must be consistent with the topics per page on regular user board index.
        $topicsPerPage = $this->container->getParameter('ccdn_forum_forum.board.show.topics_per_page');

        $crumbs = $this->container->get('ccdn_component_crumb.trail')
            ->add($this->container->get('translator')->trans('ccdn_forum_admin.crumbs.dashboard.admin', array(), 'CCDNForumAdminBundle'), $this->container->get('router')->generate('ccdn_component_dashboard_show', array('category' => 'admin')), "sitemap")
            ->add($this->container->get('translator')->trans('ccdn_forum_admin.crumbs.category.index', array(), 'CCDNForumAdminBundle'), $this->container->get('router')->generate('ccdn_forum_admin_category_index'), "home");

        return $this->container->get('templating')->renderResponse('CCDNForumAdminBundle:Category:index.html.' . $this->getEngine(), array(
            'user_profile_route' => $this->container->getParameter('ccdn_forum_admin.user.profile_route'),
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
        /*
         *	Invalidate this action / redirect if user should not have access to it
         */
        if ( ! $this->container->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('You do not have permission to access this page!');
        }

        $user = $this->container->get('security.context')->getToken()->getUser();

        $formHandler = $this->container->get('ccdn_forum_admin.category.form.insert.handler');

        if ($formHandler->process()) {
            $this->container->get('session')->setFlash('notice', $this->container->get('translator')->trans('ccdn_forum_admin.flash.category.create.success', array(), 'CCDNForumAdminBundle'));

            return new RedirectResponse($this->container->get('router')->generate('ccdn_forum_admin_category_index'));
        } else {
            // setup crumb trail.
            $crumbs = $this->container->get('ccdn_component_crumb.trail')
                ->add($this->container->get('translator')->trans('ccdn_forum_admin.crumbs.dashboard.admin', array(), 'CCDNForumAdminBundle'), $this->container->get('router')->generate('ccdn_component_dashboard_show', array('category' => 'admin')), "sitemap")
                ->add($this->container->get('translator')->trans('ccdn_forum_admin.crumbs.category.index', array(), 'CCDNForumAdminBundle'), $this->container->get('router')->generate('ccdn_forum_admin_category_index'), "home")
                ->add($this->container->get('translator')->trans('ccdn_forum_admin.crumbs.category.create', array(), 'CCDNForumAdminBundle'), $this->container->get('router')->generate('ccdn_forum_admin_category_create'), "edit");

            return $this->container->get('templating')->renderResponse('CCDNForumAdminBundle:Category:create.html.' . $this->getEngine(), array(
                'crumbs' => $crumbs,
                'form' => $formHandler->getForm()->createView(),
            ));
        }
    }

    /**
     *
     * @access public
     * @param Int $categoryId
     * @return RedirectResponse|RenderResponse
     */
    public function editAction($categoryId)
    {
        /*
         *	Invalidate this action / redirect if user should not have access to it
         */
        if ( ! $this->container->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('You do not have permission to access this page!');
        }

        $user = $this->container->get('security.context')->getToken()->getUser();

        $category = $this->container->get('ccdn_forum_forum.category.repository')->findOneById($categoryId);

        if (! $category) {
            throw new NotFoundHTTPException('category not found!');
        }

        $formHandler = $this->container->get('ccdn_forum_admin.category.form.update.handler')->setDefaultValues(array('category_entity' => $category));

        if ($formHandler->process()) {
            $this->container->get('session')->setFlash('notice', $this->container->get('translator')->trans('ccdn_forum_admin.flash.category.edit.success', array(), 'CCDNForumAdminBundle'));

            return new RedirectResponse($this->container->get('router')->generate('ccdn_forum_admin_category_index'));
        } else {
            // setup crumb trail.
            $crumbs = $this->container->get('ccdn_component_crumb.trail')
                ->add($this->container->get('translator')->trans('ccdn_forum_admin.crumbs.dashboard.admin', array(), 'CCDNForumAdminBundle'), $this->container->get('router')->generate('ccdn_component_dashboard_show', array('category' => 'admin')), "sitemap")
                ->add($this->container->get('translator')->trans('ccdn_forum_admin.crumbs.category.index', array(), 'CCDNForumAdminBundle'), $this->container->get('router')->generate('ccdn_forum_admin_category_index'), "home")
                ->add($this->container->get('translator')->trans('ccdn_forum_admin.crumbs.category.edit', array('%category_name%' => $category->getName()), 'CCDNForumAdminBundle'), $this->container->get('router')->generate('ccdn_forum_admin_category_edit', array('categoryId' => $categoryId)), "edit");

            return $this->container->get('templating')->renderResponse('CCDNForumAdminBundle:Category:edit.html.' . $this->getEngine(), array(
                'category' => $category,
                'crumbs' => $crumbs,
                'form' => $formHandler->getForm()->createView(),
            ));
        }
    }

    /**
     *
     * @access public
     * @param Int $categoryId
     * @return RedirectResponse|RenderResponse
     */
    public function deleteAction($categoryId)
    {
        if ( ! $this->container->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('You do not have permission to access this page!');
        }

        $category = $this->container->get('ccdn_forum_forum.category.repository')->findOneById($categoryId);

        if (! $category) {
            throw new NotFoundHTTPException('category not found!');
        }

        // setup crumb trail.
        $crumbs = $this->container->get('ccdn_component_crumb.trail')
            ->add($this->container->get('translator')->trans('ccdn_forum_admin.crumbs.dashboard.admin', array(), 'CCDNForumAdminBundle'), $this->container->get('router')->generate('ccdn_component_dashboard_show', array('category' => 'admin')), "sitemap")
            ->add($this->container->get('translator')->trans('ccdn_forum_admin.crumbs.category.index', array(), 'CCDNForumAdminBundle'), $this->container->get('router')->generate('ccdn_forum_admin_category_index'), "home")
            ->add($this->container->get('translator')->trans('ccdn_forum_admin.crumbs.category.delete', array('%category_name%' => $category->getName()), 'CCDNForumAdminBundle'), $this->container->get('router')->generate('ccdn_forum_admin_category_delete', array('categoryId' => $category->getId())), "trash");

        return $this->container->get('templating')->renderResponse('CCDNForumAdminBundle:Category:delete_category.html.' . $this->getEngine(), array(
            'category' => $category,
            'crumbs' => $crumbs,
        ));
    }

    /**
     *
     * @access public
     * @param Int $categoryId
     * @return RedirectResponse
     */
    public function deleteConfirmedAction($categoryId)
    {
        if ( ! $this->container->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('You do not have permission to access this page!');
        }

        $category = $this->container->get('ccdn_forum_forum.category.repository')->findOneById($categoryId);

        if (! $category) {
            throw new NotFoundHTTPException('category not found!');
        }

        $this->container->get('ccdn_forum_admin.category.manager')->remove($category)->flush();

        $this->container->get('session')->setFlash('notice', $this->container->get('translator')->trans('ccdn_forum_admin.flash.category.delete.success', array(), 'CCDNForumAdminBundle'));

        return new RedirectResponse($this->container->get('router')->generate('ccdn_forum_admin_category_index'));
    }

    /**
     *
     * @access public
     * @param Int $categoryId, String $direction
     * @return RedirectResponse
     */
    public function reorderAction($categoryId, $direction)
    {
        if ( ! $this->container->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('You do not have permission to access this page!');
        }

        $categories = $this->container->get('ccdn_forum_forum.category.repository')->findCategoriesOrderedByPriority();

        if (! $categories) {
            return new RedirectResponse($this->container->get('router')->generate('ccdn_forum_admin_category_index'));
        }

        $categoryCount = count($categories);

        // if there is only 1 category, it cannot be re-ordered.
        if ($categoryCount < 2) {
            return new RedirectResponse($this->container->get('router')->generate('ccdn_forum_admin_category_index'));
        }

        $this->container->get('ccdn_forum_admin.category.manager')->reorder($categories, $categoryId, $direction)->flush();

        $this->container->get('session')->setFlash('notice', $this->container->get('translator')->trans('ccdn_forum_admin.flash.category.reorder.success', array(), 'CCDNForumAdminBundle'));

        return new RedirectResponse($this->container->get('router')->generate('ccdn_forum_admin_category_index'));
    }

    /**
     *
     * @access public
     * @return String
     */
    protected function getEngine()
    {
        return $this->container->getParameter('ccdn_forum_admin.template.engine');
    }

}
