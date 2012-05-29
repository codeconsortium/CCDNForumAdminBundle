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
		
		$postsToDelete = array();
		$topicsToDelete = array();
		$boardsToUpdate = array();
		
		foreach($posts as $post)
		{
			$topic = $post->getTopic();

			if ($post->getTopic())
			{
				$topic = $post->getTopic();

				if ($topic->getFirstPost())
				{
					if ($topic->getFirstPost()->getId() == $post->getId())
					{
						$topic->setFirstPost(null);
						$this->persist($topic);
						
						$this->flushNow();
						
						if ( ! array_key_exists($topic->getId(), $topicsToDelete))
						{
							$topicsToDelete[$topic->getId()] = $topic;
						}
					}
				}

				if ($topic->getBoard())
				{
					$board = $topic->getBoard();

					if ($board->getLastPost())
					{
						if ($board->getLastPost()->getId() == $post->getId())
						{
							$board->setLastPost(null);		
							$this->persist($board);
						}
					}
				}
				
				if ($topic->getLastPost())
				{
					// we need to unlink a topics last post
					// to avoid an integrity constraint.
					if ($topic->getLastPost()->getId() == $post->getId())
					{
						$topic->setLastPost(null);
						$this->persist($topic);
					}
				}

				// if we remove the last post we need to update board stats.
				if ($topic->getBoard())
				{
					if ( ! array_key_exists($topic->getBoard()->getId(), $boardsToUpdate))
					{
						$boardsToUpdate[$topic->getBoard()->getId()] = $topic->getBoard();
					}
				}
			}
			
			if ( ! array_key_exists($post->getId(), $postsToDelete))
			{
				$postsToDelete[$post->getId()] = $post;
			}
		}
	
		$this->flushNow();

		foreach($postsToDelete as $post)
		{
			$this->remove($post);
		}

		$this->flushNow();
		
		foreach($topicsToDelete as $topic)
		{
			$this->update($topic);
			
			if ($topic)
			{
				$this->remove($topic);
			}
		}
		
		$this->flushNow();
		
		$boardManager = $this->container->get('ccdn_forum_forum.board.manager');
		
		foreach($boardsToUpdate as $board)
		{
			$boardManager->updateBoardStats($board);
		}
				
		$boardManager->flushNow();
				
		return $this;
	}
	
}