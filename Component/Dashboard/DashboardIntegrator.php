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

/**
 * 
 * @author Reece Fowell <reece@codeconsortium.com> 
 * @version 1.0
 */
class DashboardIntegrator extends BaseIntegrator
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
	 */
	public function getResources()
	{
		$resources = array(
			'admin' => array(
				'Forum Administration' => array(
					'cc_admin_forum_category_index' => array('auth' => 'ROLE_ADMIN', 'url' => $this->baseUrl . '/' . $this->locale . '/admin/forum/category/index', 'name' => 'Edit Categories', 'icon' => $this->basePath . '/bundles/ccdncomponentcommon/images/icons/Black/32x32/32x32_category.png'),
				),
			),

		);
		
		return $resources;
	}
	
}
