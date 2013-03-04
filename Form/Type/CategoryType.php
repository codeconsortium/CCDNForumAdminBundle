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
class CategoryType extends AbstractType
{

    /**
     *
     * @access public
     * @param FormBuilderInterface $builder, array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', null, array(
            'label' => 'ccdn_forum_admin.form.label.category.name',
			'translation_domain' =>  'CCDNForumAdminBundle',
        ));
    }

    /**
     *
     * @access public
     * @param array $options
     */
    public function getDefaultOptions(array $options)
    {
        return array(
            'empty_data' => new \CCDNForum\ForumBundle\Entity\Category(),
            'data_class' => 'CCDNForum\ForumBundle\Entity\Category',
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            // a unique key to help generate the secret token
            'intention'       => 'category_item',
            'validation_groups' => 'admin_category',
        );
    }

    /**
     *
     * @access public
     * @return string
     */
    public function getName()
    {
        return 'Category';
    }

}
