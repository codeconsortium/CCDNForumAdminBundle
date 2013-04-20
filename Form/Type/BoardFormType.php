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

namespace CCDNForum\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 *
 * @author Reece Fowell <reece@codeconsortium.com>
 * @version 1.0
 */
class BoardFormType extends AbstractType
{
	/**
	 *
	 * @access protected
	 * @var string $boardClass
	 */
	protected $boardClass;
	
	/**
	 *
	 * @access public
	 * @var string $boardClass
	 */
	public function __construct($boardClass)
	{
		$this->boardClass = $boardClass;
	}
	
    /**
     *
     * @access public
     * @param FormBuilderInterface $builder, array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
			->add('category', 'entity',
				array(
		            'property'           => 'name',
		            'class'              => 'CCDNForum\ForumBundle\Entity\Category',
		            'query_builder'      => function($repository) { return $repository->createQueryBuilder('c')->orderBy('c.id', 'ASC'); },
		            //'preferred_choices' => $this->getPreferredCategory(),
					'label'              => 'ccdn_forum_admin.form.label.board.category',
					'translation_domain' =>  'CCDNForumAdminBundle',
		        )
			)
	        ->add('name', null,
				array(
		        	'label'              => 'ccdn_forum_admin.form.label.board.name',
					'translation_domain' =>  'CCDNForumAdminBundle',
		        )
			)
	        ->add('description', 'bb_editor',
				array(
		        	'label'              => 'ccdn_forum_admin.form.label.board.description',
					'translation_domain' =>  'CCDNForumAdminBundle',
		        )
			)
	        ->add('readAuthorisedRoles', 'choice',
				array(
		            'required'           => false,
		            'expanded'           => true,
		            'multiple'           => true,
		            'choices'            => $options['available_roles'],
		            'label'              => 'View Board Roles:',
					'translation_domain' =>  'CCDNForumAdminBundle',
		        )
			)
	        ->add('topicCreateAuthorisedRoles', 'choice',
				array(
		            'required'           => false,
		            'expanded'           => true,
		            'multiple'           => true,
		            'choices'            => $options['available_roles'],
		            'label'              => 'Topic Create Roles:',
					'translation_domain' =>  'CCDNForumAdminBundle',
		        )
			)
	        ->add('topicReplyAuthorisedRoles', 'choice',
				array(
		            'required'           => false,
		            'expanded'           => true,
		            'multiple'           => true,
		            'choices'            => $options['available_roles'],
		            'label'              => 'Topic Reply Roles:',
					'translation_domain' =>  'CCDNForumAdminBundle',
		        )
			)
		;
    }

    /**
     *
     * @access public
     * @param array $options
     */
    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class'         => $this->boardClass,
            'csrf_protection'    => true,
            'csrf_field_name'    => '_token',
            // a unique key to help generate the secret token
            'intention'          => 'board_item',
            'validation_groups'  => 'admin_board',
            'available_roles'    => array('admin_board_create', 'admin_board_update'),
        );
    }

    /**
     *
     * @access public
     * @return string
     */
    public function getName()
    {
        return 'Board';
    }
}