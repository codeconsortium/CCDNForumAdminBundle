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

namespace CCDNForum\AdminBundle\Form\Handler;

use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;

use CCDNForum\ForumBundle\Manager\BaseManagerInterface;

use CCDNForum\ForumBundle\Entity\Board;

/**
 *
 * @category CCDNForum
 * @package  AdminBundle
 *
 * @author   Reece Fowell <reece@codeconsortium.com>
 * @license  http://opensource.org/licenses/MIT MIT
 * @version  Release: 2.0
 * @link     https://github.com/codeconsortium/CCDNForumAdminBundle
 *
 */
class BoardUpdateFormHandler
{
    /**
     *
     * @access protected
     * @var \Symfony\Component\Form\FormFactory $factory
     */
    protected $factory;

    /**
     *
     * @access protected
     * @var \CCDNForum\AdminBundle\Form\Type\BoardFormType $boardFormType
     */
    protected $boardFormType;

    /**
     *
     * @access protected
     * @var \CCDNForum\ForumBundle\Manager\BaseManagerInterface $boardManager
     */
    protected $boardManager;

    /**
     *
     * @access protected
     * @var \CCDNForum\ForumBundle\Manager\BaseManagerInterface $categoryManager
     */
    protected $categoryManager;

    /**
     *
     * @access protected
     * @var \CCDNForum\AdminBundle\Form\Type\BoardFormType $form
     */
    protected $form;

    /**
     *
     * @access protected
     * @var \CCDNForum\ForumBundle\Entity\Board $board
     */
    protected $board;

    /**
     *
     * @access protected
     * @var Array $roleHierarchy
     */
    protected $roleHierarchy;

    /**
     *
     * @access public
     * @param \Symfony\Component\Form\FormFactory                 $factory
     * @param \CCDNForum\AdminBundle\Form\Type\BoardFormType      $boardFormType
     * @param \CCDNForum\ForumBundle\Manager\BaseManagerInterface $boardManager
     * @param \CCDNForum\ForumBundle\Manager\BaseManagerInterface $categoryManager
     */
    public function __construct(FormFactory $factory, $boardFormType, BaseManagerInterface $boardManager, BaseManagerInterface $categoryManager)
    {
        $this->factory = $factory;
        $this->boardFormType = $boardFormType;
        $this->boardManager = $boardManager;
        $this->categoryManager = $categoryManager;
    }

    /**
     *
     * @access public
     * @param  \CCDNForum\ForumBundle\Entity\Board                        $board
     * @return \CCDNForum\AdminBundle\Form\Handler\BoardUpdateFormHandler
     */
    public function setBoard(Board $board)
    {
        $this->board = $board;

        return $this;
    }

    /**
     *
     * @access public
     * @param  Array                                                      $roleHierarchy
     * @return \CCDNForum\AdminBundle\Form\Handler\BoardUpdateFormHandler
     */
    public function setRoleHierarchy(Array $roleHierarchy)
    {
        $this->roleHierarchy = $roleHierarchy;

        return $this;
    }

    /**
     *
     * @access public
     * @param  \Symfony\Component\HttpFoundation\Request $request
     * @return bool
     */
    public function process(Request $request)
    {
        $this->getForm();

        if ($request->getMethod() == 'POST') {
            $this->form->bind($request);

            if ($this->form->isValid()) {
                $formData = $this->form->getData();

                if ($this->getSubmitAction($request) == 'post') {
                    $this->onSuccess($formData);

                    return true;
                }
            }
        }

        return false;
    }

    /**
     *
     * @access public
     * @param  \Symfony\Component\HttpFoundation\Request $request
     * @return string
     */
    public function getSubmitAction(Request $request)
    {
        if ($request->request->has('submit')) {
            $action = key($request->request->get('submit'));
        } else {
            $action = 'post';
        }

        return $action;
    }

    /**
     *
     * @access public
     * @return Form
     */
    public function getForm()
    {
        if (null == $this->form) {
            $options = array(
                'available_roles' => $this->roleHierarchy,
                'categories' => $this->categoryManager->findAllCategories(),
            );

            $this->form = $this->factory->create($this->boardFormType, $this->board, $options);
        }

        return $this->form;
    }

    /**
     *
     * @access protected
     * @param  \CCDNForum\ForumBundle\Entity\Board         $board
     * @return \CCDNForum\ForumBundle\Manager\BoardManager
     */
    protected function onSuccess(Board $board)
    {
        return $this->boardManager->updateBoard($board)->flush();
    }
}
