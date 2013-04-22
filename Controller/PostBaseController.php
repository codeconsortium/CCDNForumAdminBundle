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
class PostBaseController extends BaseController
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

        $posts = $this->getPostManager()->findThesePostsById($itemIds);

        if (! $posts || empty($posts)) {
            $this->setFlash('notice', $this->trans('flash.post.no_posts_found'));

            return;
        }

        $submitAction = $this->getSubmitAction();

        $user = $this->getUser();

        if ($submitAction == 'lock') {
            $this->getPostManager()->bulkLock($posts, $user)->flush();
        }
        if ($submitAction == 'unlock') {
            $this->getPostManager()->bulkUnlock($posts)->flush();
        }
        if ($submitAction == 'restore') {
            $this->getPostManager()->bulkRestore($posts)->flush();
        }
        if ($submitAction == 'soft_delete') {
            $this->getPostManager()->bulkSoftDelete($posts, $user)->flush();
        }
        if ($submitAction == 'hard_delete') {
            $this->getPostManager()->bulkHardDelete($posts)->flush();
        }
    }
}
