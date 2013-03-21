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

/**
 *
 * @author Reece Fowell <reece@codeconsortium.com>
 * @version 1.0
 */
class CategoryBaseController extends BaseController
{
	/**
	 *
	 * @access protected
	 * @return \CCDNForum\AdminBundle\Form\Handler\CategoryCreateFormHandler
	 */
	protected function getFormHandlerToCreateCategory()
	{
		$formHandler = $this->container->get('ccdn_forum_admin.form.handler.category_create');

		return $formHandler;
	}
	
	/**
	 *
	 * @access protected
	 * @param \CCDNForum\ForumBundle\Entity\Category $category
	 * @return \CCDNForum\AdminBundle\Form\Handler\CategoryUpdateFormHandler
	 */
	protected function getFormHandlerToEditCategory(Category $category)
	{
		$formHandler = $this->container->get('ccdn_forum_admin.form.handler.category_update');
		
		$formHandler->setCategory($category);

		return $formHandler;
	}
}