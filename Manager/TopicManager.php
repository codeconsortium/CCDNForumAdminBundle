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

namespace CCDNForum\AdminBundle\Manager;

use CCDNForum\AdminBundle\Manager\BaseManagerInterface;
use CCDNForum\AdminBundle\Manager\BaseManager;

/**
 *
 * @author Reece Fowell <reece@codeconsortium.com>
 * @version 1.0
 */
class TopicManager extends BaseManager implements BaseManagerInterface
{
    /**
     *
     * @access public
     * @param Topic $topic
     * @return self
     */
    public function restore($topic)
    {
        $topic->setIsDeleted(false);
        $topic->setDeletedBy(null);
        $topic->setDeletedDate(null);

        $this->persist($topic)->flush();

        // Update affected Topic stats.
        $this->container->get('ccdn_forum_forum.manager.topic')->updateStats($topic);

        return $this;
    }

    /**
     *
     * @access public
     * @param Topic $topic, $user
     * @return self
     */
    public function softDelete($topic, $user)
    {
        // Don't overwite previous users accountability.
        if ( ! $topic->getDeletedBy() && ! $topic->getDeletedDate()) {
            $topic->setIsDeleted(true);
            $topic->setDeletedBy($user);
            $topic->setDeletedDate(new \DateTime());

            // Close the topic as a precaution.
            $topic->setIsClosed(true);
            $topic->setClosedBy($user);
            $topic->setClosedDate(new \DateTime());

            // update the record before doing record counts
            $this->persist($topic)->flush();

            // Update affected Topic stats.
            $this->container->get('ccdn_forum_forum.manager.topic')->updateStats($topic);
        }

        return $this;
    }

    /**
     *
     * @access public
     * @param Topic $topic
     * @return self
     */
	public function hardDelete($topic)
	{
        $usersPostCountToUpdate = array();

		// Add the board of the topic to be updated.
		if ($topic->getBoard()) {
			$boardToUpdate = $topic->getBoard();
		}

		// Add author of each post to chain of cached post counts to update.
		if (count($topic->getPosts()) > 0) {
			foreach($topic->getPosts() as $postKey => $post) {
				if ($post->getCreatedBy()) {
			         $author = $post->getCreatedBy();

			         if ( ! array_key_exists($author->getId(), $usersPostCountToUpdate)) {
			             $usersPostCountToUpdate[$author->getId()] = $author;
			         }
			     }
			}
		}

		$this->remove($topic);

        $this->flush();

        // Update all affected Board stats.
		if (is_object($boardToUpdate) && $boardToUpdate instanceof CCDNForum\ForumBundle\Entity\Board) {
	        $this->container->get('ccdn_forum_forum.manager.board')->bulkUpdateStats(array($boardToUpdate))->flush();
		}
		
        // Update all affected Users cached post counts.
        if (count($usersPostCountToUpdate) > 0) {
			$this->container->get('ccdn_forum_forum.manager.registry')->bulkUpdateCachePostCountForUser($usersPostCountToUpdate);
		}

		return $this;
	}

    /**
     *
     * @access public
     * @param array $topics
     * @return self
     */
    public function bulkClose($topics, $user)
    {
        foreach ($topics as $topic) {
            // Don't overwite previous users accountability.
            if ( ! $topic->getClosedBy() && ! $topic->getClosedDate()) {
                $topic->setIsClosed(true);
                $topic->setClosedBy($user);
                $topic->setClosedDate(new \DateTime());

                $this->persist($topic);
            }
        }

        return $this;
    }

    /**
     *
     * @access public
     * @param array $topics
     * @return self
     */
    public function bulkReopen($topics)
    {
        foreach ($topics as $topic) {
            $topic->setIsClosed(false);
            $topic->setClosedBy(null);
            $topic->setClosedDate(null);

            $this->persist($topic);
        }

        return $this;
    }

    /**
     *
     * @access public
     * @param array $topics
     * @return self
     */
    public function bulkRestore($topics)
    {
        $boardsToUpdate = array();
		$usersPostCountToUpdate = array();

        foreach ($topics as $topic) {
            // Add the board of the topic to be updated.
            if ($topic->getBoard()) {
                if ( ! array_key_exists($topic->getBoard()->getId(), $boardsToUpdate)) {
                    $boardsToUpdate[$topic->getBoard()->getId()] = $topic->getBoard();
                }
            }


            // Add author of each post to chain of cached post counts to update.
            if (count($topic->getPosts()) > 0) {
				foreach($topic->getPosts() as $postKey => $post) {
					if ($post->getCreatedBy()) {
	                    $author = $post->getCreatedBy();

	                    if ( ! array_key_exists($author->getId(), $usersPostCountToUpdate)) {
	                        $usersPostCountToUpdate[$author->getId()] = $author;
	                    }
	                }
				}
            }
	
			// Remove deletion attributes.
            $topic->setIsDeleted(false);
            $topic->setDeletedBy(null);
            $topic->setDeletedDate(null);

            $this->persist($topic);
        }

        $this->flush();

        if (count($boardsToUpdate) > 0) {
            // Update all affected board stats.
            $this->container->get('ccdn_forum_forum.manager.board')->bulkUpdateStats($boardsToUpdate)->flush();
        }

        // Update all affected Users cached post counts.
        if (count($usersPostCountToUpdate) > 0) {
			$this->container->get('ccdn_forum_forum.manager.registry')->bulkUpdateCachePostCountForUser($usersPostCountToUpdate);
		}
		
        return $this;
    }

    /**
     *
     * @access public
     * @param array $topics
     * @return self
     */
    public function bulkSoftDelete($topics, $user)
    {
        $boardsToUpdate = array();
		$usersPostCountToUpdate = array();
		
        foreach ($topics as $topic) {
            // Don't overwite previous users accountability.
            if ( ! $topic->getDeletedBy() && ! $topic->getDeletedDate()) {
                // Add the board of the topic to be updated.
                if ($topic->getBoard()) {
                    if ( ! array_key_exists($topic->getBoard()->getId(), $boardsToUpdate)) {
                        $boardsToUpdate[$topic->getBoard()->getId()] = $topic->getBoard();
                    }
                }

	            // Add author of each post to chain of cached post counts to update.
	            if (count($topic->getPosts()) > 0) {
					foreach($topic->getPosts() as $postKey => $post) {
						if ($post->getCreatedBy()) {
		                    $author = $post->getCreatedBy();

		                    if ( ! array_key_exists($author->getId(), $usersPostCountToUpdate)) {
		                        $usersPostCountToUpdate[$author->getId()] = $author;
		                    }
		                }
					}
	            }

				// Set the deletion attributes.
                $topic->setIsDeleted(true);
                $topic->setDeletedBy($user);
                $topic->setDeletedDate(new \DateTime());

                $this->persist($topic);
            }
        }

        $this->flush();

        if (count($boardsToUpdate) > 0) {
            // Update all affected board stats.
            $this->container->get('ccdn_forum_forum.manager.board')->bulkUpdateStats($boardsToUpdate)->flush();
        }

        // Update all affected Users cached post counts.
        if (count($usersPostCountToUpdate) > 0) {
			$this->container->get('ccdn_forum_forum.manager.registry')->bulkUpdateCachePostCountForUser($usersPostCountToUpdate);
		}
		
        return $this;
    }

    /**
     *
     * @access public
     * @param array $topics
     * @return self
     */
    public function bulkHardDelete($topics)
    {
        $boardsToUpdate = array();
        $usersPostCountToUpdate = array();

        // Remove topics.
        foreach ($topics as $topic) {
            // Add the board of the topic to be updated.
            if ($topic->getBoard()) {
                if ( ! array_key_exists($topic->getBoard()->getId(), $boardsToUpdate)) {
                    $boardsToUpdate[$topic->getBoard()->getId()] = $topic->getBoard();
                }
            }

            // Add author of each post to chain of cached post counts to update.
            if (count($topic->getPosts()) > 0) {
				foreach($topic->getPosts() as $postKey => $post) {
					if ($post->getCreatedBy()) {
	                    $author = $post->getCreatedBy();

	                    if ( ! array_key_exists($author->getId(), $usersPostCountToUpdate)) {
	                        $usersPostCountToUpdate[$author->getId()] = $author;
	                    }
	                }
				}
            }

            $this->remove($topic);
        }

        $this->flush();

        // Update all affected Board stats.
		if (count($boardsToUpdate) > 0) {
	        $this->container->get('ccdn_forum_forum.manager.board')->bulkUpdateStats($boardsToUpdate)->flush();
		}
		
        // Update all affected Users cached post counts.
        if (count($usersPostCountToUpdate) > 0) {
			$this->container->get('ccdn_forum_forum.manager.registry')->bulkUpdateCachePostCountForUser($usersPostCountToUpdate);
		}

        return $this;
    }
}
