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
class PostManager extends ModeratorBundle\Manager\PostManager implements ManagerInterface
{
	
	
	
	/**
	 *
	 * @access public
	 * @param $posts 
	 * @return $this
	 */
	public function bulkHardDelete($posts)
	{
		
		$posts_to_delete = array();
		$topics_to_delete = array();
		$boards_to_update = array();
		$users_post_count_to_update = array();
		
		foreach($posts as $post)
		{
			if ($post->getTopic())
			{
				$topic = $post->getTopic();

				// If post is the topics last post unlink it.
				if ($topic->getLastPost())
				{
					if ($topic->getLastPost()->getId() == $post->getId())
					{
						$topic->setLastPost(null);
						
						// If post is topics last, it is likely linked as 
						// last on the board too if it is the last topic.
						if ($topic->getBoard())
						{
							$board = $topic->getBoard();
							
							if ($board->getLastPost()->getId() == $post->getId())
							{
								// Add the board of the topic to be updated.				
								if ( ! array_key_exists($board->getId(), $boards_to_update))
								{
									$boards_to_update[$board->getId()] = $board;
								}
								
								$board->setLastPost(null);
								
								$this->persist($board);
							}
						}
					}
				}
				
				// If post is the topics first post unlink it.
				if ($topic->getFirstPost())
				{
					if ($topic->getFirstPost()->getId() == $post->getId())
					{
						// We will hard delete the topic too
						// if it is the only post in the topic.
						if ($topic->getCacheReplyCount() < 1)
						{
							if ( ! array_key_exists($topic->getId(), $topics_to_delete))
							{
								$topics_to_delete[$topic->getId()] = $topic;
							}
						}
						
						$topic->setFirstPost(null);
					}
				}
				
				// Finally unlink the post from the topic.
				$post->setTopic(null);
				
				// Flush all the changes to the topic as we go.
				$this->persist($topic)->flushNow();			
			}
			
			// Add post to the delete chain
			if ( ! array_key_exists($post->getId(), $posts_to_delete))
			{
				$posts_to_delete[$post->getId()] = $post;
			}
			
			// Add author to chain of cached post counts to update.
			if ($post->getCreatedBy())
			{
				$author = $post->getCreatedBy();
				
				if ( ! array_key_exists($author->getId(), $users_post_count_to_update))
				{
					$users_post_count_to_update[$author->getId()] = $author;						
				}
			}
		}
	
		// Flush all the unlinking.
		$this->flushNow();

		// Drop the post records from the db.
		foreach($posts_to_delete as $post)
		{
			$this->refresh($post);
			
			if ($post)
			{
				$this->remove($post);
			}
		}

		$this->flushNow();
		
		// Drop the topic records from the db.
		foreach($topics_to_delete as $topic)
		{
			$this->refresh($topic);
			
			if ($topic)
			{
				$this->remove($topic);
			}
		}
		
		$this->flushNow();
		
		// Update all affected Board stats.
		$this->container->get('ccdn_forum_forum.board.manager')->bulkUpdateStats($boards_to_update)->flushNow();
		
		// Update all affected Users cached post counts.
		$this->container->get('ccdn_forum_forum.registry.manager')->bulkUpdateCachePostCountForUser($users_post_count_to_update);
				
		return $this;
	}
	
}