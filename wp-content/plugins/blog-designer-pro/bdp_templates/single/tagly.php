<?php
/**
 * The template for displaying all single posts
 * This template can be overridden by copying it to yourtheme/bdp_templates/single/tagly.php.
 * @author  Solwin Infotech
 * @version 2.0
 */

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

add_action('bd_single_design_format_function', 'bdp_single_tagly_template', 10, 1);

if (!function_exists('bdp_single_tagly_template')) {

    function bdp_single_tagly_template($bdp_settings) {
        global $post;
        $left_after = '';
        do_action('bdp_before_single_post_content');
        ?>
        <div class="blog_template bdp_blog_template tagly">
            <?php
            if (has_post_thumbnail() && isset($bdp_settings['display_thumbnail']) && $bdp_settings['display_thumbnail'] == 1) {
                ?>
                <div class="bdp-post-image">
                    <?php
                    $single_post_image = bdp_get_the_single_post_thumbnail($bdp_settings, get_post_thumbnail_id(), get_the_ID());
                    echo apply_filters('bdp_single_post_thumbnail_filter', $single_post_image, get_the_ID());
                    if (isset($bdp_settings['pinterest_image_share']) && $bdp_settings['pinterest_image_share'] == 1) {
                        ?>
                        <div class="bdp-pinterest-share-image">
                            <?php $img_url = wp_get_attachment_url(get_post_thumbnail_id($post->ID)); ?>
                            <a target="_blank" href="<?php echo 'https://pinterest.com/pin/create/button/?url=' . get_permalink($post->ID) . '&media=' . $img_url; ?>"></a>
                        </div>
                    <?php } ?>
                </div><?php
            }
            ?>

            <div class="post-content-wrapper">
                <div class="left-side-area">
                    <?php
                    if (isset($bdp_settings['display_postlike']) && $bdp_settings['display_postlike'] == 1) {
                        echo do_shortcode('[likebtn_shortcode]');
                    }

                    if ($bdp_settings['display_comment'] == 1) {
                        if (!post_password_required() && ( comments_open() || get_comments_number() )) :
                            ?>
                            <span class="comment">
                                <?php
                                if (isset($bdp_settings['disable_link_comment']) && $bdp_settings['disable_link_comment'] == 1) {
                                    ?>
                                    <a href="#">
                                    <?php } else { ?>
                                        <a href="<?php comments_link(); ?>">
                                        <?php } ?>
                                        <i class="fas fa-comment-o"></i>
                                    </a>
                            </span>
                            <?php
                        endif;
                    }
                    ?>

                    <div class="social-share social-share-div">
                        <i class="far fa-paper-plane"></i>
                        <?php bdp_get_social_icons($bdp_settings); ?>
                    </div>
                </div>


                <div class="right-side-area">
                    <?php
                    if ($bdp_settings['display_category'] == 1) {
                        $categories_list = get_the_category_list(', ');
                        $categories_link = (isset($bdp_settings['disable_link_category']) && $bdp_settings['disable_link_category'] == 1) ? true : false;
                        ?>
                        <span class="categories <?php echo ($categories_link) ? 'bdp_no_links' : 'bdp_has_links'; ?>">
                            <?php
                            if ($categories_link) {
                                $categories_list = strip_tags($categories_list);
                            }
                            if ($categories_list):
                                echo ' ';
                                print_r($categories_list);
                                echo ' ';
                            endif;
                            ?>
                        </span>
                        <?php
                    }


                    $display_title = (isset($bdp_settings['display_title']) && $bdp_settings['display_title'] != '') ? $bdp_settings['display_title'] : 1;
                    if ($display_title == 1) {
                        ?>
                        <h1 class="bdp_post_title">
                            <?php echo get_the_title(); ?>
                        </h1>
                        <?php
                    }

                    if ($bdp_settings['display_author'] == 1 || $bdp_settings['display_date'] == 1 || $bdp_settings['display_comment'] == 1) {
                        ?>
                        <div class="metadatabox">
                            <?php
                            if ($bdp_settings['display_author'] == 1) {
                                $author_link = (isset($bdp_settings['disable_link_author']) && $bdp_settings['disable_link_author'] == 1) ? false : true;
                                ?>
                                <span class="author <?php echo ($author_link) ? 'bdp_has_links' : 'bdp_no_links'; ?>">
                                    <i class="fas fa-user"></i>
                                    <?php echo bdp_get_post_auhtors($post->ID, $bdp_settings); ?>
                                </span>
                                <?php
                            }

                            if ($bdp_settings['display_date'] == 1) {
                                $date_link = (isset($bdp_settings['disable_link_date']) && $bdp_settings['disable_link_date'] == 1) ? false : true;
                                $date_format = (isset($bdp_settings['post_date_format']) && $bdp_settings['post_date_format'] != 'default') ? $bdp_settings['post_date_format'] : get_option('date_format');
                                $bdp_date = (isset($bdp_settings['dsiplay_date_from']) && $bdp_settings['dsiplay_date_from'] == 'modify') ? apply_filters('bdp_date_format', get_post_modified_time($date_format, $post->ID), $post->ID) : apply_filters('bdp_date_format', get_the_time($date_format, $post->ID), $post->ID);
                                $ar_year = get_the_time('Y');
                                $ar_month = get_the_time('m');
                                $ar_day = get_the_time('d');
                                echo '<span class="date"><i class="far fa-clock"></i>';
                                echo ($date_link) ? '<a href="' . get_day_link($ar_year, $ar_month, $ar_day) . '">' : '';
                                echo $bdp_date;
                                echo ($date_link) ? '</a>' : '';
                                echo '</span>';
                            }

                            if ($bdp_settings['display_comment'] == 1) {
                                if (!post_password_required() && ( comments_open() || get_comments_number() )) :
                                    ?>
                                    <span class="comment">
                                        <i class="fas fa-comment"></i>
                                        <?php
                                        $comment_link = (isset($bdp_settings['disable_link_comment']) && $bdp_settings['disable_link_comment'] == 1) ? false : true;
                                        bdp_comment_count($comment_link); //comments_popup_link('0', '1', '%');
                                        ?>
                                    </span>
                                    <?php
                                endif;
                            }
                            ?>
                        </div>
                        <?php
                    }
                    ?>

                    <div class="post_content entry-content">
                        <?php
                        if (isset($bdp_settings['firstletter_big']) && $bdp_settings['firstletter_big'] == 1) {
                            $content = bdp_add_first_letter_structure(get_the_content());
                            $content = apply_filters('the_content', $content);
                            $content = str_replace(']]>', ']]&gt;', $content);
                            echo $content;
                        } else {
                            the_content();
                        }
                        ?>
                    </div>

                    <div class="tagly-footer">
                        <?php
                        if ($bdp_settings['display_tag'] == 1) {
                            $tags_list = get_the_tag_list('', ', ');
                            $tag_link = (isset($bdp_settings['disable_link_tag']) && $bdp_settings['disable_link_tag'] == 1) ? true : false;
                            $class = ($tag_link) ? 'bdp_no_links' : 'bdp_has_links';
                            if ($tag_link) {
                                $tags_list = strip_tags($tags_list);
                            }

                            if ($tags_list) {
                                echo '<div class="post-tags '. $class .'">';
                                ?><span class="link-lable"> <?php _e('Tags', BLOGDESIGNERPRO_TEXTDOMAIN); ?>:&nbsp; </span><?php
                                print_r($tags_list);
                                echo '</div>';
                            }
                        }

                        if (isset($bdp_settings['display_post_views']) && $bdp_settings['display_post_views'] != 0) {
                            if (bdp_get_post_views(get_the_ID(), $bdp_settings) != '') {
                                echo '<div class="display_post_views">';
                                echo bdp_get_post_views(get_the_ID(), $bdp_settings);
                                echo '</div>';
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
        do_action('bdp_after_single_post_content');
        add_action('bdp_author_detail', 'bdp_display_author_image', 5, 1);
        add_action('bdp_author_detail', 'bdp_display_author_content_cover_start', 10, 1);
        add_action('bdp_author_detail', 'bdp_display_author_name', 15, 4);
        add_action('bdp_author_detail', 'bdp_display_author_biography', 20, 2);
        add_action('bdp_author_detail', 'bdp_display_author_social_links', 25, 4);
        add_action('bdp_author_detail', 'bdp_display_author_content_cover_end', 30, 2);
        add_action('bdp_related_post_detail', 'bdp_related_post_title', 5, 4);
        add_action('bdp_related_post_detail', 'bdp_related_post_item', 10, 9);
    }

}