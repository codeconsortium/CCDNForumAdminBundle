<?php

/*
 * This file is part of the CCDN ForumBundle
 *
 * (c) CCDN (c) CodeConsortium <http://www.codeconsortium.com/> 
 * 
 * Available on github <http://www.github.com/codeconsortium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CCDNForum\AdminBundle\Manager;

use CCDNComponent\CommonBundle\Manager\ManagerInterface;
use CCDNForum\ModeratorBundle;

/**
 * 
 * @author Reece Fowell <reece@codeconsortium.com> 
 * @version 1.0
 */
class TopicManager extends ModeratorBundle\Manager\TopicManager implements ManagerInterface
{
	
	
	
	/**
	 *
	 * @access public
	 * @param $topics 
	 * @return $this
	 */
	public function bulkHardDelete($topics)
	{
		foreach($topics as $topic)
		{
			$this->remove($topic);
		}
		
		return $this;
	}
	
}