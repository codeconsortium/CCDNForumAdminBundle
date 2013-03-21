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

namespace CCDNForum\AdminBundle\Component\Dashboard;

use CCDNComponent\DashboardBundle\Component\Integrator\BaseIntegrator;
use CCDNComponent\DashboardBundle\Component\Integrator\IntegratorInterface;

/**
 *
 * @author Reece Fowell <reece@codeconsortium.com>
 * @version 1.0
 */
class DashboardIntegrator extends BaseIntegrator implements IntegratorInterface
{

    /**
     *
     * Structure of $resources
     * 	[DASHBOARD_PAGE <string>]
     * 		[CATEGORY_NAME <string>]
     *			[ROUTE_FOR_LINK <string>]
     *				[AUTH <string>] (optional)
     *				[URL_LINK <string>]
     *				[URL_NAME <string>]
	 * 
	 * @access public
	 * @return array $resources
     */
    public function getResources()
    {
        $resources = array(
            'admin' => array(
                'Forum Administration' => array(
                    'ccdn_forum_admin_category_index' => array('auth' => 'ROLE_ADMIN', 'name' => 'Edit Categories', 'icon' => $this->basePath . '/bundles/ccdncomponentcommon/images/icons/Black/32x32/32x32_category.png'),
                    'ccdn_forum_admin_topic_deleted_show' => array('auth' => 'ROLE_ADMIN', 'name' => 'Deleted Topics', 'icon' => $this->basePath . '/bundles/ccdncomponentcommon/images/icons/Black/32x32/32x32_discussion.png'),
                    'ccdn_forum_admin_post_deleted_show' => array('auth' => 'ROLE_ADMIN', 'name' => 'Deleted Posts', 'icon' => $this->basePath . '/bundles/ccdncomponentcommon/images/icons/Black/32x32/32x32_discussion.png'),
                    'ccdn_forum_admin_topic_show_all_closed' => array('auth' => 'ROLE_MODERATOR', 'name' => 'Closed Topics', 'icon' => $this->basePath . '/bundles/ccdncomponentcommon/images/icons/Black/32x32/32x32_lock.png'),
                    'ccdn_forum_admin_post_show_all_locked' => array('auth' => 'ROLE_MODERATOR', 'name' => 'Locked Posts', 'icon' => $this->basePath . '/bundles/ccdncomponentcommon/images/icons/Black/32x32/32x32_lock.png'),
                ),
            ),

        );

        return $resources;
    }

}
