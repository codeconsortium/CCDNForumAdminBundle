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

        $user = $this->getUser();

        $posts = $this->container->get('ccdn_forum_forum.repository.post')->findThesePostsByIdForModeration($itemIds);

        if ( ! $posts || empty($posts)) {
            $this->setFlash('notice', $this->trans('flash.post.no_posts_found'));

            return;
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
    }
}