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

use CCDNForum\AdminBundle\Controller\PostBaseController;

/**
 *
 * @author Reece Fowell <reece@codeconsortium.com>
 * @version 1.0
 */
class PostController extends PostBaseController
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

        $postsPager = $this->getPostManager()->findLockedPostsForModeratorsPaginated($page);

        // setup crumb trail.
        $crumbs = $this->getCrumbs()
            ->add($this->trans('ccdn_forum_admin.crumbs.post.show_locked'), $this->path('ccdn_forum_admin_post_locked_show_all'));

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

        $postsPager = $this->getPostManager()->findDeletedPostsForAdminsPaginated($page);

        // setup crumb trail.
        $crumbs = $this->getCrumbs()
            ->add($this->trans('ccdn_forum_admin.crumbs.post.show_deleted'), $this->path('ccdn_forum_admin_post_deleted_show_all'));

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
    public function lockedBulkAction()
    {
        $this->isAuthorised('ROLE_ADMIN');

		$this->bulkAction();

        return $this->redirectResponse($this->path('ccdn_forum_admin_post_locked_show_all'));
    }
	
    /**
     *
     * @access public
     * @return RedirectResponse
     */
    public function deletedBulkAction()
    {
        $this->isAuthorised('ROLE_ADMIN');

		$this->bulkAction();

        return $this->redirectResponse($this->path('ccdn_forum_admin_post_deleted_show_all'));
    }
}