<?php
class PadmaIframeDummyContent {

    public static function init() {

        add_action('wp', array(__CLASS__, 'inject_wp_query'));

    }


    public static function get_entry_content($post_type_text = 'entry') {

        return <<<HTML
<p><strong>This is sample content from Padma</strong> to give you an idea how a <em>Single $post_type_text</em> will look.</p>

<h2>Header Level 2</h2>

<p>Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. Aenean ultricies mi vitae est. Mauris placerat eleifend leo. Quisque sit amet est et sapien ullamcorper pharetra. Vestibulum erat wisi, condimentum sed, commodo vitae, ornare sit amet, wisi. Aenean fermentum, elit eget tincidunt condimentum, eros ipsum rutrum orci, sagittis tempus lacus enim ac dui. Donec non enim in turpis pulvinar facilisis. Ut felis. Praesent dapibus, neque id cursus faucibus, tortor neque egestas augue, eu vulputate magna eros eu erat. Aliquam erat volutpat. Nam dui mi, tincidunt quis, accumsan porttitor, facilisis luctus, metus</p>

<ol>
   <li>Lorem ipsum dolor sit amet, consectetuer adipiscing elit.</li>
   <li>Aliquam tincidunt mauris eu risus.</li>
</ol>

<blockquote><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus magna. Cras in mi at felis aliquet congue. Ut a est eget ligula molestie gravida. Curabitur massa. Donec eleifend, libero at sagittis mollis, tellus est malesuada tellus, at luctus turpis elit sit amet quam. Vivamus pretium ornare est.</p></blockquote>

<h3>Header Level 3</h3>

<ul>
   <li>Lorem ipsum dolor sit amet, consectetuer adipiscing elit.</li>
   <li>Aliquam tincidunt mauris eu risus.</li>
</ul>
HTML;

    }


    public static function inject_wp_query() {

        global $wp_query;

        if ( !empty($wp_query->posts) ) {
            return false;
        }

        $layout_fragments = explode(PadmaLayout::$sep, PadmaLayout::get_current());

        if ( count($layout_fragments) > 2 ) {
            return false;
        }

        add_filter('old_slug_redirect_url', '__return_false', 12);

        switch ( $layout_fragments[0] ) {

            /* Singles */
            case 'single':

                $is_single_only = isset($layout_fragments[1]) ? false : true;
                $post_type = isset($layout_fragments[1]) ? $layout_fragments[1] : 'post';
                $post_type_obj = get_post_type_object($post_type);

                $post_type_text = $is_single_only ? 'Entry' : ucwords($post_type_obj->labels->singular_name);

                $wp_query = new WP_Query();

                $wp_query->init();

                $wp_query->is_single = true;
                $wp_query->is_404 = false;

                $sample_entry = new WP_Post(new stdClass());

                $sample_entry->ID = rand(1, 999);
                $sample_entry->post_title = 'Sample Single Entry';
                $sample_entry->post_author = get_current_user_id();
                $sample_entry->post_content = self::get_entry_content($post_type_text);
                $sample_entry->post_status = 'publish';
                $sample_entry->post_name = 'sample-single-entry';
                $sample_entry->post_date = date('Y-m-d H:i:s', time());
                $sample_entry->post_modified = date('Y-m-d H:i:s', time());
                $sample_entry->guid = home_url('/#' .  rand(1, 999));
                $sample_entry->post_type = $post_type;

                $wp_query->posts = array(
                    $sample_entry
                );

                update_post_caches(
                    $wp_query->posts,
                    'post'
                );

                $wp_query->post = $wp_query->posts[0];

                $wp_query->post_count = count($wp_query->posts);
                $wp_query->found_posts = $wp_query->post_count;

                break;

        }

    }

}