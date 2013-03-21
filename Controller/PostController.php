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
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use CCDNForum\AdminBundle\Controller\BaseController;

/**
 *
 * @author Reece Fowell <reece@codeconsortium.com>
 * @version 1.0
 */
class PostController extends BaseController
{
    /**
     *
     * Display a list of locked posts (locked from editing)
     *
     * @access public
     * @param int $page
     * @return RenderResponse
     */
    public function showLockedAction($page)
    {
        $this->isAuthorised('ROLE_MODERATOR');

        $postsPager = $this->container->get('ccdn_forum_forum.repository.post')->findLockedPostsForModeratorsPaginated();

        $postsPerPage = $this->container->getParameter('ccdn_forum_admin.post.show_locked.posts_per_page');
        $postsPager->setMaxPerPage($postsPerPage);
        $postsPager->setCurrentPage($page, false, true);

        // setup crumb trail.
        $crumbs = $this->getCrumbs()
            ->add($this->trans('ccdn_forum_admin.crumbs.dashboard.admin'), $this->path('ccdn_component_dashboard_show', array('category' => 'moderator')), "sitemap")
            ->add($this->trans('ccdn_forum_admin.crumbs.post.show_locked'), $this->path('ccdn_forum_admin_post_show_all_locked'), "home");

        return $this->renderResponse('CCDNForumAdminBundle:Post:show_locked.html.', array(
            'crumbs' => $crumbs,
            'posts' => $postsPager,
            'pager' => $postsPager,
        ));
    }

    /**
     *
     * Display a list of deleted posts.
     *
     * @access public
     * @param int $page
     * @return RenderResponse
     */
    public function showDeletedAction($page)
    {
        $this->isAuthorised('ROLE_ADMIN');

        $postsPager = $this->container->get('ccdn_forum_forum.repository.post')->findDeletedPostsForAdminsPaginated();

        $postsPerPage = $this->container->getParameter('ccdn_forum_admin.post.show_deleted.posts_per_page');
        $postsPager->setMaxPerPage($postsPerPage);
        $postsPager->setCurrentPage($page, false, true);

        // setup crumb trail.
        $crumbs = $this->getCrumbs()
            ->add($this->trans('ccdn_forum_admin.crumbs.dashboard.admin'), $this->path('ccdn_component_dashboard_show', array('category' => 'admin')), "sitemap")
            ->add($this->trans('ccdn_forum_admin.crumbs.post.show_deleted'), $this->path('ccdn_forum_admin_post_deleted_show'), "trash");

        return $this->renderResponse('CCDNForumAdminBundle:Post:show_deleted.html.', array(
            'crumbs' => $crumbs,
            'posts' => $postsPager,
            'pager' => $postsPager,
        ));
    }

    /**
     *
     * @access public
     * @return RedirectResponse
     */
    public function bulkAction()
    {
        $this->isAuthorised('ROLE_ADMIN');

        // Get all the checked item id's.
        $itemIds = array();
        $ids = $_POST;
        foreach ($ids as $itemKey => $itemId) {
            if (substr($itemKey, 0, 6) == 'check_') {
                //
                // Cast the key values to int upon extraction.
                //
                $id = (int) substr($itemKey, 6, (strlen($itemKey) - 6));

                if (is_int($id) == true) {
                    $itemIds[] = $id;
                }
            }
        }

        // Don't bother if there are no checkboxes to process.
        if (count($itemIds) < 1) {
            return new RedirectResponse($this->path('ccdn_forum_admin_post_deleted_show'));
        }

        $user = $this->getUser();

        $posts = $this->container->get('ccdn_forum_forum.repository.post')->findThesePostsByIdForModeration($itemIds);

        if ( ! $posts || empty($posts)) {
            $this->setFlash('notice', $this->trans('flash.post.no_posts_found'));

            return new RedirectResponse($this->path('ccdn_forum_admin_post_deleted_show'));
        }

        if (isset($_POST['submit_lock'])) {
            $this->container->get('ccdn_forum_admin.manager.post')->bulkLock($posts, $user)->flush();
        }
        if (isset($_POST['submit_unlock'])) {
            $this->container->get('ccdn_forum_admin.manager.post')->bulkUnlock($posts)->flush();
        }
        if (isset($_POST['submit_restore'])) {
            $this->container->get('ccdn_forum_admin.manager.post')->bulkRestore($posts)->flush();
        }
        if (isset($_POST['submit_soft_delete'])) {
            $this->container->get('ccdn_forum_admin.manager.post')->bulkSoftDelete($posts, $user)->flush();
        }
        if (isset($_POST['submit_hard_delete'])) {
            $this->container->get('ccdn_forum_admin.manager.post')->bulkHardDelete($posts)->flush();
        }

        return new RedirectResponse($this->path('ccdn_forum_admin_post_deleted_show'));
    }
}