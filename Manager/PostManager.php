<?php

/*
 * This file is part of the CCDNForum ForumBundle
 *
 * (c) CCDN (c) CodeConsortium <http://www.codeconsortium.com/>
 *
 * Available on github <http://www.github.com/codeconsortium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CCDNForum\AdminBundle\Manager;

use CCDNForum\AdminBundle\Manager\ManagerInterface;
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
        $topicsToUpdate = array();
        $boardsToUpdate = array();
        $usersPostCountToUpdate = array();

        foreach ($posts as $post) {
            if ($post->getTopic()) {
                $topic = $post->getTopic();

                // If post is the topics last post unlink it.
                if ($topic->getLastPost()) {
                    if ($topic->getLastPost()->getId() == $post->getId()) {
                        $topic->setLastPost(null);

                        // Add the topic to the topics to be updated list.
                        if ( ! array_key_exists($topic->getId(), $topicsToUpdate)) {
                            $topicsToUpdate[$topic->getId()] = $topic;
                        }

                        // If post is topics last, it is likely linked as
                        // last on the board too if it is the last topic.
                        if ($topic->getBoard()) {
                            $board = $topic->getBoard();

                            if ($board->getLastPost()->getId() == $post->getId()) {
                                // Add the board of the topic to be updated.
                                if ( ! array_key_exists($board->getId(), $boardsToUpdate)) {
                                    $boardsToUpdate[$board->getId()] = $board;
                                }

                                $board->setLastPost(null);

                                $this->persist($board);
                            }
                        }
                    }
                }

                // If post is the topics first post unlink it.
                if ($topic->getFirstPost()) {
                    if ($topic->getFirstPost()->getId() == $post->getId()) {
                        $topic->setFirstPost(null);

                        // We will hard delete the topic too
                        // if it is the only post in the topic.
                        if ($topic->getCachedReplyCount() < 1) {
                            if ( ! array_key_exists($topic->getId(), $topicsToDelete)) {
                                $topicsToDelete[$topic->getId()] = $topic;

                                if (array_key_exists($topic->getId(), $topicsToUpdate)) {
                                    unset($topicsToUpdate[$topic->getId()]);
                                }
                            }
                        } else {
                            // Add the topic to the topics to be updated list.
                            if ( ! array_key_exists($topic->getId(), $topicsToUpdate)) {
                                $topicsToUpdate[$topic->getId()] = $topic;
                            }
                        }
                    }
                }

                // Finally unlink the post from the topic.
                $post->setTopic(null);

                // Flush all the changes to the topic as we go.
                $this->persist($topic)->flush();
            }

            // Add post to the delete chain
            if ( ! array_key_exists($post->getId(), $postsToDelete)) {
                $postsToDelete[$post->getId()] = $post;
            }

            // Add author to chain of cached post counts to update.
            if ($post->getCreatedBy()) {
                $author = $post->getCreatedBy();

                if ( ! array_key_exists($author->getId(), $usersPostCountToUpdate)) {
                    $usersPostCountToUpdate[$author->getId()] = $author;
                }
            }
        }

        // Flush all the unlinking.
        $this->flush();

        // Drop the post records from the db.
        foreach ($postsToDelete as $post) {
            $this->refresh($post);

            if ($post) {
                $this->remove($post);
            }
        }

        $this->flush();

        // Drop the topic records from the db.
        foreach ($topicsToDelete as $topic) {
            $this->refresh($topic);

            if ($topic) {
                $this->remove($topic);
            }
        }

        $this->flush();

        // Update all affected Board stats.
        $this->container->get('ccdn_forum_forum.board.manager')->bulkUpdateStats($boardsToUpdate)->flush();

        // Update all affected Topic stats.
        $this->container->get('ccdn_forum_forum.topic.manager')->bulkUpdateStats($topicsToUpdate)->flush();

        // Update all affected Users cached post counts.
        $this->container->get('ccdn_forum_forum.registry.manager')->bulkUpdateCachePostCountForUser($usersPostCountToUpdate);

        return $this;
    }

}
