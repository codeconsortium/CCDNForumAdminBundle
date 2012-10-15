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

/**
 *
 * @author Reece Fowell <reece@codeconsortium.com>
 * @version 1.0
 */
class TopicController extends ContainerAware
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
        if ( ! $this->container->get('security.context')->isGranted('ROLE_MODERATOR')) {
            throw new AccessDeniedException('You do not have access to this section.');
        }

        $user = $this->container->get('security.context')->getToken()->getUser();

        $topicsPager = $this->container->get('ccdn_forum_forum.repository.topic')->findClosedTopicsForModeratorsPaginated();

        $topicsPerPage = $this->container->getParameter('ccdn_forum_admin.topic.show_closed.topics_per_page');
        $topicsPager->setMaxPerPage($topicsPerPage);
        $topicsPager->setCurrentPage($page, false, true);

        // setup crumb trail.
        $crumbs = $this->container->get('ccdn_component_crumb.trail')
            ->add($this->container->get('translator')->trans('ccdn_forum_admin.crumbs.dashboard.admin', array(), 'CCDNForumAdminBundle'), $this->container->get('router')->generate('ccdn_component_dashboard_show', array('category' => 'moderator')), "sitemap")
            ->add($this->container->get('translator')->trans('ccdn_forum_admin.crumbs.topic.show_closed', array(), 'CCDNForumAdminBundle'), $this->container->get('router')->generate('ccdn_forum_admin_topic_show_all_closed'), "home");

        return $this->container->get('templating')->renderResponse('CCDNForumAdminBundle:Topic:show_closed.html.' . $this->getEngine(), array(
            'crumbs' => $crumbs,
            'user_profile_route' => $this->container->getParameter('ccdn_forum_admin.user.profile_route'),
            'user' => $user,
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
        if ( ! $this->container->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('You do not have access to this section.');
        }

        $user = $this->container->get('security.context')->getToken()->getUser();

        $topicsPager = $this->container->get('ccdn_forum_forum.repository.topic')->findClosedTopicsForModeratorsPaginated();

        $topicsPerPage = $this->container->getParameter('ccdn_forum_admin.topic.show_deleted.topics_per_page');
        $topicsPager->setMaxPerPage($topicsPerPage);
        $topicsPager->setCurrentPage($page, false, true);

        // setup crumb trail.
        $crumbs = $this->container->get('ccdn_component_crumb.trail')
            ->add($this->container->get('translator')->trans('ccdn_forum_admin.crumbs.dashboard.admin', array(), 'CCDNForumAdminBundle'), $this->container->get('router')->generate('ccdn_component_dashboard_show', array('category' => 'admin')), "sitemap")
            ->add($this->container->get('translator')->trans('ccdn_forum_admin.crumbs.topic.show_deleted', array(), 'CCDNForumAdminBundle'), $this->container->get('router')->generate('ccdn_forum_admin_topic_deleted_show'), "trash");

        return $this->container->get('templating')->renderResponse('CCDNForumAdminBundle:Topic:show_deleted.html.' . $this->getEngine(), array(
            'user_profile_route' => $this->container->getParameter('ccdn_forum_admin.user.profile_route'),
            'user' => $user,
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
        if ( ! $this->container->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('You do not have access to this section.');
        }

        //
        // Get all the checked item id's.
        //
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

        //
        // Don't bother if there are no checkboxes to process.
        //
        if (count($itemIds) < 1) {
            return new RedirectResponse($this->container->get('router')->generate('ccdn_forum_admin_topic_deleted_show'));
        }

        $user = $this->container->get('security.context')->getToken()->getUser();

        $topics = $this->container->get('ccdn_forum_forum.repository.topic')->findTheseTopicsByIdForModeration($itemIds);

        if ( ! $topics || empty($topics)) {
            $this->container->get('session')->setFlash('notice', $this->container->get('translator')->trans('flash.topic.no_topics_found', array(), 'CCDNForumAdminBundle'));

            return new RedirectResponse($this->container->get('router')->generate('ccdn_forum_admin_topic_deleted_show'));
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

        return new RedirectResponse($this->container->get('router')->generate('ccdn_forum_admin_topic_deleted_show'));
    }













    /**
     *
     * @access public
     * @param int $topicId
     * @return RedirectResponse
     */
    public function stickyAction($topicId)
    {
        if ( ! $this->container->get('security.context')->isGranted('ROLE_MODERATOR')) {
            throw new AccessDeniedException('You do not have access to this section.');
        }

        $user = $this->container->get('security.context')->getToken()->getUser();

        $topic = $this->container->get('ccdn_forum_forum.repository.topic')->find($topicId);

        if (! $topic) {
            throw new NotFoundHttpException('No such topic exists!');
        }

        $this->container->get('ccdn_forum_admin.manager.topic')->sticky($topic, $user)->flush();

        $this->container->get('session')->setFlash('success', $this->container->get('translator')->trans('ccdn_forum_admin.flash.topic.sticky.success', array('%topic_title%' => $topic->getTitle()), 'CCDNForumAdminBundle'));

        return new RedirectResponse($this->container->get('router')->generate('ccdn_forum_forum_topic_show', array('topicId' => $topic->getId()) ));
    }

    /**
     *
     * @access public
     * @param int $topicId
     * @return RedirectResponse
     */
    public function unstickyAction($topicId)
    {
        if ( ! $this->container->get('security.context')->isGranted('ROLE_MODERATOR')) {
            throw new AccessDeniedException('You do not have access to this section.');
        }

        $topic = $this->container->get('ccdn_forum_forum.repository.topic')->find($topicId);

        if (! $topic) {
            throw new NotFoundHttpException('No such topic exists!');
        }

        $this->container->get('ccdn_forum_admin.manager.topic')->unsticky($topic)->flush();

        $this->container->get('session')->setFlash('notice', $this->container->get('translator')->trans('ccdn_forum_admin.flash.topic.unsticky.success', array('%topic_title%' => $topic->getTitle()), 'CCDNForumAdminBundle'));

        return new RedirectResponse($this->container->get('router')->generate('ccdn_forum_forum_topic_show', array('topicId' => $topic->getId()) ));
    }

    /**
     *
     * Once a topic is locked, no posts can be added, deleted or edited!
     *
     * @access public
     * @param int $topicId
     * @return RedirectResponse
     */
    public function closeAction($topicId)
    {
        if ( ! $this->container->get('security.context')->isGranted('ROLE_MODERATOR')) {
            throw new AccessDeniedException('You do not have access to this section.');
        }

        $user = $this->container->get('security.context')->getToken()->getUser();

        $topic = $this->container->get('ccdn_forum_forum.repository.topic')->find($topicId);

        if (! $topic) {
            throw new NotFoundHttpException('No such topic exists!');
        }

        $this->container->get('ccdn_forum_admin.manager.topic')->close($topic, $user)->flush();

        $this->container->get('session')->setFlash('warning', $this->container->get('translator')->trans('ccdn_forum_admin.flash.topic.close.success', array('%topic_title%' => $topic->getTitle()), 'CCDNForumAdminBundle'));

        return new RedirectResponse($this->container->get('router')->generate('ccdn_forum_forum_topic_show', array('topicId' => $topic->getId()) ));
    }

    /**
     *
     * @access public
     * @param int $topicId
     * @return RedirectResponse
     */
    public function reopenAction($topicId)
    {
        if ( ! $this->container->get('security.context')->isGranted('ROLE_MODERATOR')) {
            throw new AccessDeniedException('You do not have access to this section.');
        }

        $user = $this->container->get('security.context')->getToken()->getUser();

        $topic = $this->container->get('ccdn_forum_forum.repository.topic')->find($topicId);

        if (! $topic) {
            throw new NotFoundHttpException('No such topic exists!');
        }

        $this->container->get('ccdn_forum_admin.manager.topic')->reopen($topic)->flush();

        $this->container->get('session')->setFlash('warning', $this->container->get('translator')->trans('ccdn_forum_admin.flash.topic.reopen.success', array('%topic_title%' => $topic->getTitle()), 'CCDNForumAdminBundle'));

        return new RedirectResponse($this->container->get('router')->generate('ccdn_forum_forum_topic_show', array('topicId' => $topic->getId()) ));
    }

    /**
     *
     * @access public
     * @param int $topicId
     * @return RenderResponse
     */
    public function deleteAction($topicId)
    {
        if ( ! $this->container->get('security.context')->isGranted('ROLE_MODERATOR')) {
            throw new AccessDeniedException('You do not have permission to use this resource!');
        }

        $user = $this->container->get('security.context')->getToken()->getUser();

        $topic = $this->container->get('ccdn_forum_forum.repository.topic')->find($topicId);

        if (! $topic) {
            throw new NotFoundHttpException('No such post exists!');
        }

        $board = $topic->getBoard();
        $category = $board->getCategory();

        $crumbDelete = $this->container->get('translator')->trans('crumbs.topic.delete', array(), 'CCDNForumForumBundle');

        // setup crumb trail.
        $crumbs = $this->container->get('ccdn_component_crumb.trail')
            ->add($this->container->get('translator')->trans('crumbs.forum_index', array(), 'CCDNForumForumBundle'), $this->container->get('router')->generate('ccdn_forum_forum_category_index'), "home")
            ->add($category->getName(),	$this->container->get('router')->generate('ccdn_forum_forum_category_show', array('categoryId' => $category->getId())), "category")
            ->add($board->getName(), $this->container->get('router')->generate('ccdn_forum_forum_board_show', array('boardId' => $board->getId())), "board")
            ->add($topic->getTitle(), $this->container->get('router')->generate('ccdn_forum_forum_topic_show', array('topicId' => $topic->getId())), "communication")
            ->add($crumbDelete, $this->container->get('router')->generate('ccdn_forum_forum_topic_reply', array('topicId' => $topic->getId())), "trash");

        return $this->container->get('templating')->renderResponse('CCDNForumAdminBundle:Topic:delete_topic.html.' . $this->getEngine(), array(
            'user_profile_route' => $this->container->getParameter('ccdn_forum_admin.user.profile_route'),
            'topic' => $topic,
            'crumbs' => $crumbs,
        ));
    }

    /**
     *
     * @access public
     * @param int $topicId
     * @return RedirectResponse
     */
    public function deleteConfirmedAction($topicId)
    {
        if ( ! $this->container->get('security.context')->isGranted('ROLE_MODERATOR')) {
            throw new AccessDeniedException('You do not have permission to use this resource!');
        }

        $user = $this->container->get('security.context')->getToken()->getUser();

        $topic = $this->container->get('ccdn_forum_forum.repository.topic')->find($topicId);

        if (! $topic) {
            throw new NotFoundHttpException('No such topic exists!');
        }

        $this->container->get('ccdn_forum_admin.manager.topic')->softDelete($topic, $user)->flush();

        // set flash message
        $this->container->get('session')->setFlash('warning', $this->container->get('translator')->trans('ccdn_forum_admin.flash.topic.delete.success', array('%topic_title%' => $topic->getTitle()), 'CCDNForumAdminBundle'));

        // forward user
        return new RedirectResponse($this->container->get('router')->generate('ccdn_forum_forum_board_show', array('boardId' => $topic->getBoard()->getId()) ));
    }

    /**
     *
     * @access public
     * @param int $topicId
     * @return RedirectResponse
     */
    public function restoreAction($topicId)
    {
        if ( ! $this->container->get('security.context')->isGranted('ROLE_MODERATOR')) {
            throw new AccessDeniedException('You do not have permission to use this resource!');
        }

        $topic = $this->container->get('ccdn_forum_forum.repository.topic')->find($topicId);

        if (! $topic) {
            throw new NotFoundHttpException('No such topic exists!');
        }

        $this->container->get('ccdn_forum_admin.manager.topic')->restore($topic)->flush();

        // set flash message
        $this->container->get('session')->setFlash('notice', $this->container->get('translator')->trans('ccdn_forum_admin.flash.topic.restore.success', array('%topic_title%' => $topic->getTitle()), 'CCDNForumAdminBundle'));

        // forward user
        return new RedirectResponse($this->container->get('router')->generate('ccdn_forum_forum_board_show', array('boardId' => $topic->getBoard()->getId()) ));
    }

    /**
     *
     * @access public
     * @param int $topicId
     * @return RedirectResponse|RenderResponse
     */
    public function moveAction($topicId)
    {
        if ( ! $this->container->get('security.context')->isGranted('ROLE_MODERATOR')) {
            throw new AccessDeniedException('You do not have access to this section.');
        }

        $topic = $this->container->get('ccdn_forum_forum.repository.topic')->find($topicId);

        if (! $topic) {
            throw new NotFoundHttpException('No such topic exists!');
        }

        $formHandler = $this->container->get('ccdn_forum_admin.form.handler.change_topics_board')->setDefaultValues(array('topic' => $topic));

        if ($formHandler->process()) {
            $this->container->get('session')->setFlash('warning', $this->container->get('translator')->trans('ccdn_forum_admin.flash.topic.move.success', array('%topic_title%' => $topic->getTitle()), 'CCDNForumAdminBundle'));

            return new RedirectResponse($this->container->get('router')->generate('ccdn_forum_forum_topic_show', array('topicId' => $topic->getId()) ));
        } else {
            $board = $topic->getBoard();
            $category = $board->getCategory();

            // setup crumb trail.
            $crumbs = $this->container->get('ccdn_component_crumb.trail')
                ->add($this->container->get('translator')->trans('crumbs.forum_index', array(), 'CCDNForumForumBundle'), $this->container->get('router')->generate('ccdn_forum_forum_category_index'), "home")
                ->add($category->getName(), $this->container->get('router')->generate('ccdn_forum_forum_category_show', array('categoryId' => $category->getId())), "category")
                ->add($board->getName(), $this->container->get('router')->generate('ccdn_forum_forum_board_show', array('boardId' => $board->getId())), "board")
                ->add($topic->getTitle(), $this->container->get('router')->generate('ccdn_forum_forum_topic_show', array('topicId' => $topic->getId())), "communication")
                ->add($this->container->get('translator')->trans('ccdn_forum_admin.crumbs.topic.change_board', array(), 'CCDNForumAdminBundle'), $this->container->get('router')->generate('ccdn_forum_admin_topic_change_board', array('topicId' => $topic->getId())), "edit");

            return $this->container->get('templating')->renderResponse('CCDNForumAdminBundle:Topic:change_board.html.' . $this->getEngine(), array(
                'crumbs' => $crumbs,
                'topic' => $topic,
                'form' => $formHandler->getForm()->createView(),
            ));
        }
    }

    /**
     *
     * @access public
     * @return RedirectResponse
     */
/*    public function bulkAction()
    {
        if ( ! $this->container->get('security.context')->isGranted('ROLE_MODERATOR')) {
            throw new AccessDeniedException('You do not have access to this section.');
        }

        //
        // Get all the checked item id's.
        //
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

        //
        // Don't bother if there are no checkboxes to process.
        //
        if (count($itemIds) < 1) {
            return new RedirectResponse($this->container->get('router')->generate('ccdn_forum_admin_topic_show_all_closed'));
        }

        $user = $this->container->get('security.context')->getToken()->getUser();

        $topics = $this->container->get('ccdn_forum_forum.repository.topic')->findTheseTopicsByIdForModeration($itemIds);

        if ( ! $topics || empty($topics)) {
            $this->container->get('session')->setFlash('error', $this->container->get('translator')->trans('ccdn_forum_admin.flash.topic.none_found', array(), 'CCDNForumAdminBundle'));

            return new RedirectResponse($this->container->get('router')->generate('ccdn_forum_admin_topic_show_all_closed'));
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

        return new RedirectResponse($this->container->get('router')->generate('ccdn_forum_admin_topic_show_all_closed'));
    }
*/




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
