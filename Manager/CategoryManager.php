<?php

/*
 * This file is part of the CCDN AdminBundle
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
use CCDNComponent\CommonBundle\Manager\BaseManager;

/**
 * 
 * @author Reece Fowell <reece@codeconsortium.com> 
 * @version 1.0
 */
class CategoryManager extends BaseManager implements ManagerInterface
{
	

	
	/**
	 *
	 * @access public
	 * @param $category
	 * @return $this
	 */
	public function insert($category)
	{
		$category_count_query = $this->entityManager->createQuery('
			SELECT COUNT(c.id)
			FROM CCDNForumForumBundle:Category c');
		
		try {
			$category_count = $category_count_query->getSingleResult();
		} catch (\Doctrine\ORM\NoResultException $e) 
		{
			
		}
	
		$category->setListOrderPriority(++$category_count[1]);
		
		// insert a new row
		$this->persist($category);
		
		return $this;
	}	
	
	
	
	/**
	 *
	 * @access public
	 * @param $category
	 * @return $this
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
	 * @param $categories, $category_id, $direction
	 * @return $this
	 */
	public function reorder($categories, $category_id, $direction)
	{
		$category_count = count($categories);	
		for ($index = 0, $priority = 1, $align = false; $index < $category_count; $index++, $priority++) {
			if ($categories[$index]->getId() == $category_id) {
				if ($align == false) { // if aligning then other indices priorities are being corrected
					// **************
					// **** DOWN ****
					// **************
					if ($direction == 'down') {
						if ($index < ($category_count - 1)) { // <-- must be lower because we need to alter an offset of the next index.
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
						if ($index > 0)	{
							$categories[$index]->setListOrderPriority($priority-1); // move this up the page
							$categories[$index-1]->setListOrderPriority($priority); // move this down the page
							$index+=1; $priority++;
						} else {
							$categories[$index]->setListOrderPriority($category_count); // move to the bottom of the page
							$index = -1; $priority = -1; // alter offsets for alignment of all other priorities							
						}
					} // end down / up direction
					$align = true; continue;
				}// end align
			} else {
				$categories[$index]->setListOrderPriority($priority);
			} // end category id match
		} // end loop
		
		foreach($categories as $category) { $this->persist($category); }
		
		return $this;
	}
	
}