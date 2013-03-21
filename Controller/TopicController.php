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
class TopicController extends BaseController
{
    /**
     *
     * Displays a list of closed topics (locked from posting new posts)
     *
     * @access public
     * @param int $page
     * @return RenderResponse
     */
    public function showClosedAction($page)
    {
        $this->isAuthorised('ROLE_MODERATOR');

        $topicsPager = $this->container->get('ccdn_forum_forum.repository.topic')->findClosedTopicsForModeratorsPaginated();

        $topicsPerPage = $this->container->getParameter('ccdn_forum_admin.topic.show_closed.topics_per_page');
        $topicsPager->setMaxPerPage($topicsPerPage);
        $topicsPager->setCurrentPage($page, false, true);

        // setup crumb trail.
        $crumbs = $this->getCrumbs()
            ->add($this->trans('ccdn_forum_admin.crumbs.dashboard.admin'), $this->path('ccdn_component_dashboard_show', array('category' => 'moderator')), "sitemap")
            ->add($this->trans('ccdn_forum_admin.crumbs.topic.show_closed'), $this->path('ccdn_forum_admin_topic_show_all_closed'), "home");

        return $this->renderResponse('CCDNForumAdminBundle:Topic:show_closed.html.', array(
            'crumbs' => $crumbs,
            'topics' => $topicsPager,
            'pager' => $topicsPager,
        ));
    }

    /**
     *
     * Displays a list of soft deleted topics
     *
     * @access public
	 * @param int $page
     * @return RenderResponse
     */
    public function showDeletedAction($page)
    {
        $this->isAuthorised('ROLE_ADMIN');

        $topicsPager = $this->container->get('ccdn_forum_forum.repository.topic')->findClosedTopicsForModeratorsPaginated();

        $topicsPerPage = $this->container->getParameter('ccdn_forum_admin.topic.show_deleted.topics_per_page');
        $topicsPager->setMaxPerPage($topicsPerPage);
        $topicsPager->setCurrentPage($page, false, true);

        // setup crumb trail.
        $crumbs = $this->getCrumbs()
            ->add($this->trans('ccdn_forum_admin.crumbs.dashboard.admin'), $this->path('ccdn_component_dashboard_show', array('category' => 'admin')), "sitemap")
            ->add($this->trans('ccdn_forum_admin.crumbs.topic.show_deleted'), $this->path('ccdn_forum_admin_topic_deleted_show'), "trash");

        return $this->renderResponse('CCDNForumAdminBundle:Topic:show_deleted.html.', array(
            'crumbs' => $crumbs,
            'topics' => $topicsPager,
            'pager' => $topicsPager,
        ));
    }

    /**
     *
     * Restores/Deletes items, marked for removal from db via checkboxes for each
     * item present in a form. This can only be done via a member with role_admin!
     *
     * @access public
     * @return RedirectResponse
     */
    public function bulkAction()
    {
        $this->isAuthorised('ROLE_ADMIN');

        $itemIds = $this->getCheckedItemIds('check_');

        // Don't bother if there are no checkboxes to process.
        if (count($itemIds) < 1) {
            return new RedirectResponse($this->path('ccdn_forum_admin_topic_deleted_show'));
        }

        $user = $this->getUser();

        $topics = $this->container->get('ccdn_forum_forum.repository.topic')->findTheseTopicsByIdForModeration($itemIds);

        if ( ! $topics || empty($topics)) {
            $this->setFlash('notice', $this->trans('flash.topic.no_topics_found'));

            return new RedirectResponse($this->path('ccdn_forum_admin_topic_deleted_show'));
        }

        if (isset($_POST['submit_close'])) {
            $this->container->get('ccdn_forum_admin.manager.topic')->bulkClose($topics, $user)->flush();
        }
        if (isset($_POST['submit_reopen'])) {
            $this->container->get('ccdn_forum_admin.manager.topic')->bulkReopen($topics)->flush();
        }
        if (isset($_POST['submit_restore'])) {
            $this->container->get('ccdn_forum_admin.manager.topic')->bulkRestore($topics)->flush();
        }
        if (isset($_POST['submit_soft_delete'])) {
            $this->container->get('ccdn_forum_admin.manager.topic')->bulkSoftDelete($topics, $user)->flush();
        }
        if (isset($_POST['submit_hard_delete'])) {
            $this->container->get('ccdn_forum_admin.manager.topic')->bulkHardDelete($topics)->flush();
        }

        return new RedirectResponse($this->path('ccdn_forum_admin_topic_deleted_show'));
    }
}