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
		$boards_to_update = array();
		$users_post_count_to_update = array();
				
		// Remove topics.
		foreach($topics as $topic)
		{
			// Add the board of the topic to be updated.
			if ($topic->getBoard())
			{
				if ( ! array_key_exists($topic->getBoard()->getId(), $boards_to_update))
				{
					$boards_to_update[$topic->getBoard()->getId()] = $topic->getBoard();
				}
			}
			
			// Add author to chain of cached post counts to update.
			if ($topic->getFirstPost())
			{
				$first_post = $topic->getFirstPost();
				
				if ($first_post->getCreatedBy())
				{
					$author = $first_post->getCreatedBy();
					
					if ( ! array_key_exists($author->getId(), $users_post_count_to_update))
					{
						$users_post_count_to_update[$author->getId()] = $author;						
					}
				}
			}
			
			$this->remove($topic);
		}
		
		$this->flushNow();
		
		// Update all affected Board stats.
		$this->container->get('ccdn_forum_forum.board.manager')->bulkUpdateStats($boards_to_update)->flushNow();
		
		// Update all affected Users cached post counts.
		$this->container->get('ccdn_forum_forum.registry.manager')->bulkUpdateCachePostCountForUser($users_post_count_to_update);
		
		return $this;
	}
	
}