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
     *
     * Structure of $resources
     * 	[DASHBOARD_PAGE String]
     * 		[CATEGORY_NAME String]
     *			[ROUTE_FOR_LINK String]
     *				[AUTH String]
     *				[URL_LINK String]
     *				[URL_NAME String]
	 * 
	 * @access public
	 * @return Array()
     */
    public function getResources()
    {
        $resources = array(
            'admin' => array(
                'Forum Administration' => array(
                    'ccdn_forum_admin_category_index' => array('auth' => 'ROLE_ADMIN', 'name' => 'Edit Categories', 'icon' => $this->basePath . '/bundles/ccdncomponentcommon/images/icons/Black/32x32/32x32_category.png'),
                    'ccdn_forum_admin_topic_deleted_show' => array('auth' => 'ROLE_ADMIN', 'name' => 'Deleted Topics', 'icon' => $this->basePath . '/bundles/ccdncomponentcommon/images/icons/Black/32x32/32x32_discussion.png'),
                    'ccdn_forum_admin_post_deleted_show' => array('auth' => 'ROLE_ADMIN', 'name' => 'Deleted Posts', 'icon' => $this->basePath . '/bundles/ccdncomponentcommon/images/icons/Black/32x32/32x32_discussion.png'),
                    'ccdn_forum_admin_repair_tools' => array('auth' => 'ROLE_ADMIN', 'name' => 'Repair', 'icon' => $this->basePath . '/bundles/ccdncomponentcommon/images/icons/black/32x32/32x32_repair.png'),
                ),
            ),

        );

        return $resources;
    }

}
