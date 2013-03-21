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
class TopicBaseController extends BaseController
{
	/**
	 *
	 * @access protected
	 */
	protected function bulkAction()
	{
        $itemIds = $this->getCheckedItemIds('check_');

        // Don't bother if there are no checkboxes to process.
        if (count($itemIds) < 1) {
            return;
        }

        $user = $this->getUser();

        $topics = $this->container->get('ccdn_forum_forum.repository.topic')->findTheseTopicsByIdForModeration($itemIds);

        if ( ! $topics || empty($topics)) {
            $this->setFlash('notice', $this->trans('flash.topic.no_topics_found'));

            return;
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
	}
}