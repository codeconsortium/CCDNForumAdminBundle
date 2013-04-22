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

use CCDNForum\AdminBundle\Controller\BaseController;

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

        $topics = $this->getTopicManager()->findTheseTopicsById($itemIds);

        if (! $topics || empty($topics)) {
            $this->setFlash('notice', $this->trans('flash.topic.no_topics_found'));

            return;
        }

        $submitAction = $this->getSubmitAction();

        $user = $this->getUser();

        if ($submitAction == 'close') {
            $this->getTopicManager()->bulkClose($topics, $user)->flush();
        }
        if ($submitAction == 'reopen') {
            $this->getTopicManager()->bulkReopen($topics)->flush();
        }
        if ($submitAction == 'restore') {
            $this->getTopicManager()->bulkRestore($topics)->flush();
        }
        if ($submitAction == 'soft_delete') {
            $this->getTopicManager()->bulkSoftDelete($topics, $user)->flush();
        }
        if ($submitAction == 'hard_delete') {
            $this->getTopicManager()->bulkHardDelete($topics)->flush();
        }
    }
}
