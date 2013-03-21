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
class BoardManager extends BaseManager implements BaseManagerInterface
{

    /**
     *
     * @access public
     * @param Board $board
     * @return self
     */
    public function insert($board)
    {
		
		$boardCount = $this->container->get('ccdn_forum_forum.repository.board')->countBoardsForCategory($board->getCategory()->getId());

        $board->setListOrderPriority(++$boardCount[1]);

        // insert a new row
        $this->persist($board);

        return $this;
    }

    /**
     *
     * @access public
     * @param Board $board
     * @return self
     */
    public function update($board)
    {
        // update a record
        $this->persist($board);

        return $this;
    }

    /**
     *
     * @access public
     * @param array $boards, int $boardId, string $direction
     * @return self
     */
    public function reorder($boards, $boardId, $direction)
    {
        $boardCount = count($boards);
        for ($index = 0, $priority = 1, $align = false; $index < $boardCount; $index++, $priority++) {
            if ($boards[$index]->getId() == $boardId) {
                if ($align == false) { // if aligning then other indices priorities are being corrected
                    // **************
                    // **** DOWN ****
                    // **************
                    if ($direction == 'down') {
                        if ($index < ($boardCount - 1)) { // <-- must be lower because we need to alter an offset of the next index.
                            $boards[$index]->setListOrderPriority($priority+1); // move this down the page
                            $boards[$index+1]->setListOrderPriority($priority); // move this up the page
                            $index+=1; $priority++; // the next index has already been changed
                        } else {
                            $boards[$index]->setListOrderPriority(1); // move to the top of the page
                            $index = -1; $priority = 1; // alter offsets for alignment of all other priorities
                        }
                    // **************
                    // ***** UP *****
                    // **************
                    } else {
                        if ($index > 0) {
                            $boards[$index]->setListOrderPriority($priority-1); // move this up the page
                            $boards[$index-1]->setListOrderPriority($priority); // move this down the page
                            $index+=1; $priority++;
                        } else {
                            $boards[$index]->setListOrderPriority($boardCount); // move to the bottom of the page
                            $index = -1; $priority = -1; // alter offsets for alignment of all other priorities
                        }
                    } // end down / up direction
                    $align = true; continue;
                }// end align
            } else {
                $boards[$index]->setListOrderPriority($priority);
            } // end board id match
        } // end loop

        foreach ($boards as $board) { $this->persist($board); }

		$this->flush();
		
        return $this;
    }

}
