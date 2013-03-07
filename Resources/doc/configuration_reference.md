CCDNForum AdminBundle Configuration Reference.
==============================================

All available configuration options are listed below with their default values.

``` yml
#
# for CCDNForum AdminBundle
#
ccdn_forum_admin:
    template:
        engine:               twig
    seo:
        title_length:         67
    category:
        index:
            layout_template:      CCDNComponentCommonBundle:Layout:layout_body_right.html.twig
            last_post_datetime_format:  d-m-Y - H:i
            enable_bb_parser:     true
        create:
            layout_template:      CCDNComponentCommonBundle:Layout:layout_body_right.html.twig
            form_theme:           CCDNForumAdminBundle:Form:fields.html.twig
        edit:
            layout_template:      CCDNComponentCommonBundle:Layout:layout_body_right.html.twig
            form_theme:           CCDNForumAdminBundle:Form:fields.html.twig
        delete:
            layout_template:      CCDNComponentCommonBundle:Layout:layout_body_right.html.twig
    board:
        create:
            layout_template:      CCDNComponentCommonBundle:Layout:layout_body_right.html.twig
            form_theme:           CCDNForumAdminBundle:Form:fields.html.twig
            enable_bb_editor:     true
        edit:
            layout_template:      CCDNComponentCommonBundle:Layout:layout_body_right.html.twig
            form_theme:           CCDNForumAdminBundle:Form:fields.html.twig
            enable_bb_editor:     true
        delete:
            layout_template:      CCDNComponentCommonBundle:Layout:layout_body_right.html.twig
    topic:
        show_closed:
            layout_template:      CCDNComponentCommonBundle:Layout:layout_body_right.html.twig
            topics_per_page:      40
            topic_title_truncate:  20
            post_created_datetime_format:  d-m-Y - H:i
            topic_closed_datetime_format:  d-m-Y - H:i
            topic_deleted_datetime_format:  d-m-Y - H:i
        show_deleted:
            layout_template:      CCDNComponentCommonBundle:Layout:layout_body_right.html.twig
            topics_per_page:      40
            topic_title_truncate:  17
            topic_created_datetime_format:  d-m-Y - H:i
            topic_closed_datetime_format:  d-m-Y - H:i
            topic_deleted_datetime_format:  d-m-Y - H:i
        delete_topic:
            layout_template:      CCDNComponentCommonBundle:Layout:layout_body_right.html.twig
        change_board:
            layout_template:      CCDNComponentCommonBundle:Layout:layout_body_right.html.twig
            form_theme:           CCDNForumAdminBundle:Form:fields.html.twig
    post:
        show_locked:
            posts_per_page:       20
            topic_title_truncate:  20
            layout_template:      CCDNComponentCommonBundle:Layout:layout_body_right.html.twig
            post_created_datetime_format:  d-m-Y - H:i
            post_locked_datetime_format:  d-m-Y - H:i
            post_deleted_datetime_format:  d-m-Y - H:i
        show_deleted:
            layout_template:      CCDNComponentCommonBundle:Layout:layout_body_right.html.twig
            posts_per_page:       40
            topic_title_truncate:  17
            post_created_datetime_format:  d-m-Y - H:i
            post_locked_datetime_format:  d-m-Y - H:i
            post_deleted_datetime_format:  d-m-Y - H:i
```

- [Return back to the docs index](index.md).
