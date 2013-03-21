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
class CategoryManager extends BaseManager implements BaseManagerInterface
{

    /**
     *
     * @access public
     * @param Category $category
     * @return self
     */
    public function insert($category)
    {
		$categoryCount = $this->container->get('ccdn_forum_forum.repository.category')->countCategories();

        $category->setListOrderPriority(++$categoryCount[1]);

        // insert a new row
        $this->persist($category);

        return $this;
    }

    /**
     *
     * @access public
     * @param Category $category
     * @return self
     */
    public function update($category)
    {
        // update a record
        $this->persist($category);

        return $this;
    }

    /**
     *
     * @access public
     * @param array $categories, int $categoryId, string $direction
     * @return self
     */
    public function reorder($categories, $categoryId, $direction)
    {
        $categoryCount = count($categories);
        for ($index = 0, $priority = 1, $align = false; $index < $categoryCount; $index++, $priority++) {
            if ($categories[$index]->getId() == $categoryId) {
                if ($align == false) { // if aligning then other indices priorities are being corrected
                    // **************
                    // **** DOWN ****
                    // **************
                    if ($direction == 'down') {
                        if ($index < ($categoryCount - 1)) { // <-- must be lower because we need to alter an offset of the next index.
                            $categories[$index]->setListOrderPriority($priority+1); // move this down the page
                            $categories[$index+1]->setListOrderPriority($priority); // move this up the page
                            $index+=1; $priority++; // the next index has already been changed
                        } else {
                            $categories[$index]->setListOrderPriority(1); // move to the top of the page
                            $index = -1; $priority = 1; // alter offsets for alignment of all other priorities
                        }
                    // **************
                    // ***** UP *****
                    // **************
                    } else {
                        if ($index > 0) {
                            $categories[$index]->setListOrderPriority($priority-1); // move this up the page
                            $categories[$index-1]->setListOrderPriority($priority); // move this down the page
                            $index+=1; $priority++;
                        } else {
                            $categories[$index]->setListOrderPriority($categoryCount); // move to the bottom of the page
                            $index = -1; $priority = -1; // alter offsets for alignment of all other priorities
                        }
                    } // end down / up direction
                    $align = true; continue;
                }// end align
            } else {
                $categories[$index]->setListOrderPriority($priority);
            } // end category id match
        } // end loop

        foreach ($categories as $category) { $this->persist($category); }

		$this->flush();
		
        return $this;
    }

}
