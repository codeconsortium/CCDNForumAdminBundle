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
    protected $defaults = array();

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
     * @param FormBuilderInterface $builder, array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
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
     * @param array $defaults
     */
    public function setDefaultValues(array $defaults = null)
    {
        $this->defaults = array_merge($this->defaults, $defaults);

        return $this;
    }

    /**
     *
     * @access public
     * @param array $options
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
            'validation_groups' => 'admin_board',
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
