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

use CCDNForum\AdminBundle\Controller\TopicBaseController;

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
class TopicController extends TopicBaseController
{
    /**
     *
     * Displays a list of closed topics (locked from posting new posts)
     *
     * @access public
     * @param  int            $page
     * @return RenderResponse
     */
    public function showClosedAction($page)
    {
        $this->isAuthorised('ROLE_MODERATOR');

        $topicsPager = $this->getTopicManager()->findClosedTopicsForModeratorsPaginated($page);

        // setup crumb trail.
        $crumbs = $this->getCrumbs()
            ->add($this->trans('crumbs.topic.show_closed'), $this->path('ccdn_forum_admin_topic_closed_show_all'));

        return $this->renderResponse('CCDNForumAdminBundle:Topic:show_closed.html.',
            array(
                'crumbs' => $crumbs,
                //'topics' => $topicsPager,
                'pager' => $topicsPager,
            )
        );
    }

    /**
     *
     * Displays a list of soft deleted topics
     *
     * @access public
     * @param  int            $page
     * @return RenderResponse
     */
    public function showDeletedAction($page)
    {
        $this->isAuthorised('ROLE_ADMIN');

        $topicsPager = $this->getTopicManager()->findDeletedTopicsForAdminsPaginated($page);

        // setup crumb trail.
        $crumbs = $this->getCrumbs()
            ->add($this->trans('crumbs.topic.show_deleted'), $this->path('ccdn_forum_admin_topic_deleted_show_all'));

        return $this->renderResponse('CCDNForumAdminBundle:Topic:show_deleted.html.',
            array(
                'crumbs' => $crumbs,
                //'topics' => $topicsPager,
                'pager' => $topicsPager,
            )
        );
    }

    /**
     *
     * Restores/Deletes items, marked for removal from db via checkboxes for each
     * item present in a form. This can only be done via a member with role_admin!
     *
     * @access public
     * @return RedirectResponse
     */
    public function closedBulkAction()
    {
        $this->isAuthorised('ROLE_ADMIN');

        $this->bulkAction();

        return $this->redirectResponse($this->path('ccdn_forum_admin_topic_closed_show_all'));
    }

    /**
     *
     * Restores/Deletes items, marked for removal from db via checkboxes for each
     * item present in a form. This can only be done via a member with role_admin!
     *
     * @access public
     * @return RedirectResponse
     */
    public function deletedBulkAction()
    {
        $this->isAuthorised('ROLE_ADMIN');

        $this->bulkAction();

        return $this->redirectResponse($this->path('ccdn_forum_admin_topic_deleted_show_all'));
    }
}
