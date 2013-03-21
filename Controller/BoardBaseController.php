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

namespace CCDNForum\AdminBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use CCDNForum\AdminBundle\Controller\BaseController;

use CCDNForum\ForumBundle\Entity\Category;
use CCDNForum\ForumBundle\Entity\Board;

/**
 *
 * @author Reece Fowell <reece@codeconsortium.com>
 * @version 1.0
 */
class BoardBaseController extends BaseController
{
	/**
	 *
	 * @access protected
	 * @return Array
	 */
	protected function getRoleHierarchy()
	{
        $roleHierarchy = $this->container->getParameter('security.role_hierarchy.roles');
        
        $roles = array();
		
        foreach ($roleHierarchy as $roleName => $roleSubs) {
            $subs = '<ul><li>' . implode('</li><li>', $roleSubs) . '</li></ul>';
            $roles[$roleName] = '<strong>' . $roleName . '</strong>' . ($subs != '<ul><li>' . $roleName . '</li></ul>' ? "\n" . $subs:'');
        }
		
		return $roles;	
	}
	
	/**
	 *
	 * @access protected
	 * @param \CCDNForum\ForumBundle\Entity\Category $category
	 * @return \CCDNForum\AdminBundle\Form\Handler\BoardCreateFormHandler
	 */
	protected function getFormHandlerToCreateBoard(Category $category)
	{
		$formHandler = $this->container->get('ccdn_forum_admin.form.handler.board_create');
		
		$formHandler->setCategory($category);
		$formHandler->setRoleHierarchy($this->getRoleHierarchy());
		
		return $formHandler;
	}
	
	/**
	 *
	 * @access protected
	 * @param \CCDNForum\AdminBundle\Entity\Board $board
	 * @return \CCDNForum\AdminBundle\Form\Handler\BoardUpdateFormHandler
	 */
	protected function getFormHandlerToEditBoard(Board $board)
	{
		$formHandler = $this->container->get('ccdn_forum_admin.form.handler.board_update');
		
		$formHandler->setBoard($board);
		$formHandler->setRoleHierarchy($this->getRoleHierarchy());
		
		return $formHandler;
	}
}