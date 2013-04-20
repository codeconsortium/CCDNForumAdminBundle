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

namespace CCDNForum\AdminBundle\Component\Dashboard;

use CCDNComponent\DashboardBundle\Component\Integrator\Model\BuilderInterface;

/**
 *
 * @author Reece Fowell <reece@codeconsortium.com>
 * @version 2.0
 */
class DashboardIntegrator
{
    /**
	 * 
	 * @access public
     * @param CCDNComponent\DashboardBundle\Component\Integrator\Model\BuilderInterface $builder
     */
    public function build(BuilderInterface $builder)
    {
		$builder
			->addCategory('forum_admin')
				->setLabel('ccdn_forum_admin.dashboard.categories.forum_admin', array(), 'CCDNForumAdminBundle')
				->addPages()
					->addPage('admin')
						->setLabel('ccdn_forum_admin.dashboard.pages.admin', array(), 'CCDNForumAdminBundle')
					->end()
					->addPage('forum')
						->setLabel('ccdn_forum_admin.dashboard.pages.forum', array(), 'CCDNForumAdminBundle')
					->end()
				->end()
				->addLinks()	
					->addLink('edit_categories')
						->setAuthRole('ROLE_ADMIN')
						->setRoute('ccdn_forum_admin_category_index')
						->setIcon('/bundles/ccdncomponentcommon/images/icons/Black/32x32/32x32_category.png')
						->setLabel('ccdn_forum_admin.title.manage_boards', array(), 'CCDNForumAdminBundle')
					->end()
					->addLink('deleted_topics')
						->setAuthRole('ROLE_ADMIN')
						->setRoute('ccdn_forum_admin_topic_deleted_show_all')
						->setIcon('/bundles/ccdncomponentcommon/images/icons/Black/32x32/32x32_discussion.png')
						->setLabel('ccdn_forum_admin.title.topic.show_deleted', array(), 'CCDNForumAdminBundle')
					->end()
					->addLink('deleted_posts')
						->setAuthRole('ROLE_ADMIN')
						->setRoute('ccdn_forum_admin_post_deleted_show_all')
						->setIcon('/bundles/ccdncomponentcommon/images/icons/Black/32x32/32x32_discussion.png')
						->setLabel('ccdn_forum_admin.title.post.show_deleted', array(), 'CCDNForumAdminBundle')
					->end()
					->addLink('closed_topics')
						->setAuthRole('ROLE_MODERATOR')
						->setRoute('ccdn_forum_admin_topic_closed_show_all')
						->setIcon('/bundles/ccdncomponentcommon/images/icons/Black/32x32/32x32_lock.png')
						->setLabel('ccdn_forum_admin.title.topic.show_closed', array(), 'CCDNForumAdminBundle')
					->end()
					->addLink('locked_posts')
						->setAuthRole('ROLE_MODERATOR')
						->setRoute('ccdn_forum_admin_post_locked_show_all')
						->setIcon('/bundles/ccdncomponentcommon/images/icons/Black/32x32/32x32_lock.png')
						->setLabel('ccdn_forum_admin.title.post.show_locked', array(), 'CCDNForumAdminBundle')
					->end()
				->end()
			->end()
		;
    }
}
