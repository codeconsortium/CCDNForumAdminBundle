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

namespace CCDNForum\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

/**
 * 
 * @author Reece Fowell <reece@codeconsortium.com> 
 * @version 1.0
 */
class BoardType extends AbstractType
{
	
	
	/**
	 *
	 * @access protected
	 */
	protected $doctrine;
	
	
	/**
	 *
	 * @access protected
	 */
	protected $defaults;
	
	
	/**
	 *
	 * @access public
	 * @param $doctrine
	 */
	public function __construct($doctrine)
	{
		$this->doctrine = $doctrine;
	}
	
	
	/**
	 *
	 * @access public
	 * @param FormBuilder $builder, Array() $options
	 */
	public function buildForm(FormBuilder $builder, array $options)
	{		
		$builder->add('name');
		$builder->add('description');
		$builder->add('category', 'entity', array(
		    'class' => 'CCDNForumForumBundle:Category',
		    'query_builder' => function($repository) { return $repository->createQueryBuilder('c')->orderBy('c.id', 'ASC'); },
		    'property' => 'name',
			'preferred_choices' => array($this->defaults['category']),
		));
	}
	
	
	/**
	 *
	 * @access public
	 * @param Array() $defaults
	 */
	public function setDefaultValues($defaults)
	{
		$this->defaults = $defaults;
	}
	
	
	/**
	 *
	 * for creating and replying to topics
	 *
	 * @access public
	 * @param Array() $options
	 */	
	public function getDefaultOptions(array $options)
	{
		return array(
            'empty_data' => new \CCDNForum\ForumBundle\Entity\Board(),
			'data_class' => 'CCDNForum\ForumBundle\Entity\Board',
			'csrf_protection' => true,
            'csrf_field_name' => '_token',
            // a unique key to help generate the secret token
            'intention'       => 'board_item',
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
