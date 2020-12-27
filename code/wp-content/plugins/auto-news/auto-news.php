<?php

error_reporting(1);

/*
Plugin Name: Auto news
Plugin URI:  http://auto-news.com
Description: Lấy tin tức tự động
Version:     1.0
Author:      Nguyễn Trọng Bằng
*/

if (!defined('ABSPATH')) exit;

require_once(plugin_dir_path(__FILE__) . "vendor/autoload.php");
require_once(ABSPATH . "wp-includes/pluggable.php");
require_once(ABSPATH . 'wp-admin/includes/media.php');
require_once(ABSPATH . 'wp-admin/includes/file.php');
require_once(ABSPATH . 'wp-admin/includes/image.php');

use DOMWrap\Document;

if (!class_exists('AutoNews')) :

    class AutoNews
    {
        var $version = '1.0';

        function __construct()
        {

        }

        public function initialize()
        {
            $version = $this->version;
            $path = plugin_dir_path(__FILE__);

            $this->define('AUTONEWS', true);
            $this->define('AUTONEWS_VERSION', $version);
            $this->define('AUTONEWS_PATH', $path);

            add_action('init', array($this, 'register_post_types'), 2);
            add_action('init', array($this, 'add_filters'), 3);

            add_action('rest_api_init', function () {
                register_rest_route('auto-news/v1', '/links', array(
                    'methods' => 'POST',
                    'callback' => array($this, 'get_auto_news_links_api')
                ));
            });

            add_action('rest_api_init', function () {
                register_rest_route('auto-news/v1', '/posts', array(
                    'methods' => 'POST',
                    'callback' => array($this, 'get_auto_news_posts_api')
                ));
            });
        }

        function define($name, $value = true)
        {
            if (!defined($name)) {
                define($name, $value);
            }
        }

        function register_post_types()
        {
            $this->create_auto_news_config_post_type();
            $this->create_auto_news_link_post_type();
        }

        function add_filters()
        {
            add_filter('manage_auto_news_link_posts_columns', array($this, 'set_custom_edit_auto_news_link_columns'));
            add_action('manage_auto_news_link_posts_custom_column', array($this, 'custom_auto_news_link_column'), 10, 2);
            add_filter('manage_auto_news_config_posts_columns', array($this, 'set_custom_edit_auto_news_config_columns'));
            add_action('manage_auto_news_config_posts_custom_column', array($this, 'custom_auto_news_config_column'), 10, 2);
        }

        function create_auto_news_config_post_type()
        {
            $labels = array(
                'name' => _x('Cấu hình tin', 'post type general name', 'domain'),
                'singular_name' => _x('Cấu hình tin', 'post type singular name', 'domain'),
                'menu_name' => _x('Cấu hình tin', 'admin menu', 'domain'),
                'name_admin_bar' => _x('Cấu hình tin', 'add new on admin bar', 'domain'),
                'add_new' => _x('Thêm cấu hình tin', 'cấu hình tin', 'domain'),
                'add_new_item' => __('Thêm cấu hình tin', 'domain'),
                'new_item' => __('Thêm cấu hình tin', 'domain'),
                'edit_item' => __('Sửa cấu hình tin', 'domain'),
                'view_item' => __('Xem cấu hình tin', 'domain'),
                'all_items' => __('Cấu hình tin', 'domain'),
                'search_items' => __('Tìm cấu hình tin', 'domain'),
                'parent_item_colon' => __('Cấu hình tin cha:', 'domain'),
                'not_found' => __('Không tìm thấy cấu hình tin nào.', 'domain'),
                'not_found_in_trash' => __('Không tìm thấy cấu hình tin nào.', 'domain'),
            );

            $args = array(
                'labels' => $labels,
                'public' => true,
                'publicly_queryable' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'query_var' => true,
                'capability_type' => 'post',
                'has_archive' => true,
                'hierarchical' => false,
                'menu_position' => null,
                'supports' => array('title', 'editor')
            );

            register_post_type('auto_news_config', $args);
        }

        function create_auto_news_link_post_type()
        {
            $labels = array(
                'name' => _x('Link chạy', 'post type general name', 'domain'),
                'singular_name' => _x('Link chạy', 'post type singular name', 'domain'),
                'menu_name' => _x('Link chạy', 'admin menu', 'domain'),
                'name_admin_bar' => _x('Link chạy', 'add new on admin bar', 'domain'),
                'add_new' => _x('Thêm link chạy', 'link chạy', 'domain'),
                'add_new_item' => __('Thêm link chạy', 'domain'),
                'new_item' => __('Thêm link chạy', 'domain'),
                'edit_item' => __('Sửa link chạy', 'domain'),
                'view_item' => __('Xem link chạy', 'domain'),
                'all_items' => __('Link chạy', 'domain'),
                'search_items' => __('Tìm link chạy', 'domain'),
                'parent_item_colon' => __('Link chạy cha:', 'domain'),
                'not_found' => __('Không tìm thấy link chạy nào.', 'domain'),
                'not_found_in_trash' => __('Không tìm thấy link chạy nào.', 'domain'),
            );

            $args = array(
                'labels' => $labels,
                'public' => true,
                'publicly_queryable' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'query_var' => true,
                'capability_type' => 'post',
                'has_archive' => true,
                'hierarchical' => false,
                'menu_position' => null,
                'supports' => array('title', 'editor')
            );

            register_post_type('auto_news_link', $args);
        }

        function set_custom_edit_auto_news_link_columns($columns)
        {
            unset($columns['author']);
            unset($columns['tags']);
            unset($columns['comments']);
            $tmp = $columns['date'];
            unset($columns['date']);
            $columns['link'] = __('Link gốc', 'domain');
            $columns['config_id'] = __('Config ID', 'domain');
            $columns['post_id'] = __('Post ID', 'domain');
            $columns['status'] = __('Trạng thái', 'domain');
            $columns['date'] = $tmp;

            return $columns;
        }

        function custom_auto_news_link_column($column, $post_id)
        {
            switch ($column) {

                case 'link' :
                    echo get_field('link', $post_id, true);
                    break;

                case 'status' :
                    echo get_field('status', $post_id, true);
                    break;

                case 'config_id' :
                    echo get_field('config_id', $post_id, true);
                    break;

                case 'post_id' :
                    echo get_field('post_id', $post_id, true);
                    break;
            }
        }

        function set_custom_edit_auto_news_config_columns($columns)
        {
            unset($columns['author']);
            unset($columns['tags']);
            unset($columns['comments']);
            unset($columns['date']);
            $columns['url'] = __('URL', 'domain');
            $columns['link_xpath'] = __('Link xpath', 'domain');
            $columns['title_xpath'] = __('Title xpath', 'domain');
            $columns['content_xpath'] = __('Content xpath', 'domain');
            $columns['category'] = __('Category', 'domain');

            return $columns;
        }

        function custom_auto_news_config_column($column, $post_id)
        {
            switch ($column) {

                case 'url' :
                    echo get_field('url', $post_id, true);
                    break;

                case 'link_xpath' :
                    echo get_field('link_xpath', $post_id, true);
                    break;

                case 'title_xpath' :
                    echo get_field('title_xpath', $post_id, true);
                    break;

                case 'content_xpath' :
                    echo get_field('content_xpath', $post_id, true);
                    break;

                case 'category' :
                    $cat_names = [];
                    $cats = get_field('category', $post_id, true);
                    if (!empty($cats)) {
                        foreach ($cats as $cat_id) {
                            $cat_names[] = get_cat_name($cat_id);
                        }
                    }
                    if (!empty($cat_names)) {
                        echo implode(", ", $cat_names);
                    }
                    break;
            }
        }

        public function get_post($args)
        {
            $posts = get_posts($args);

            return count($posts) ? $posts[0] : null;
        }

        public function get_auto_news_links()
        {
            $posts = get_posts([
                "post_type" => "auto_news_config",
                "posts_per_page" => -1
            ]);

            if ($posts) {
                foreach ($posts as $post) {
                    $url = get_post_meta($post->ID, "url", true);
                    $link_xpath = get_post_meta($post->ID, "link_xpath", true);
                    $html = file_get_contents($url);

                    $doc = new Document();
                    $doc->html($html);

                    $link_nodes = $doc->find($link_xpath);

                    if ($link_nodes) {
                        foreach ($link_nodes as $link_node) {
                            $link = $link_node->attr("href");

                            if (strpos($link, "http") === false) {
                                $tmp = parse_url($url);
                                $link = $tmp["scheme"] . "://" . $tmp["host"] . $link;
                            }

                            $link_existed = $this->get_post([
                                "post_type" => "auto_news_link",
                                "meta_key" => "link",
                                "meta_value" => $link
                            ]);

                            if (!$link_existed) {
                                $this->insert_auto_news_link([
                                    "title" => $link,
                                    "meta_input" => [
                                        "link" => $link,
                                        "status" => "init",
                                        "config_id" => $post->ID
                                    ],
                                ]);
                            }
                        }
                    }
                }
            }
        }

        public function get_auto_news_posts()
        {
            $posts = get_posts([
                "post_type" => "auto_news_link",
                "posts_per_page" => -1,
                "meta_query" => array(
                    'clause_1' => array(
                        'key' => 'status',
                        'value' => 'init'
                    )
                )
            ]);

            if ($posts) {
                foreach ($posts as $post) {
                    update_post_meta($post->ID, "status", "processing");
                    $link = get_post_meta($post->ID, "link", true);
                    $config_id = get_post_meta($post->ID, "config_id", true);
                    $title_xpath = get_post_meta($config_id, "title_xpath", true);
                    $content_xpath = get_post_meta($config_id, "content_xpath", true);
                    $category = get_field('category', $config_id, true);

                    $html = file_get_contents($link);

                    $doc = new Document();
                    $doc->html($html);

                    $title = "";
                    $content = "";

                    /**
                     * Title
                     */
                    $title_node = $doc->find($title_xpath);

                    if ($title_node) {
                        $title = $title_node->text();
                    }

                    /**
                     * Content
                     */
                    $content_node = $doc->find($content_xpath);

                    if ($content_node) {
                        $content = $content_node->html();
                    }

                    if (!empty($title)) {
                        $post_id = $this->insert_post([
                                "title" => $title,
                                "content" => $content,
                                "category" => $category,
                                "meta_input" => [
                                    "link" => $link,
                                    "parent_id" => $post->ID,
                                ]
                            ]
                        );

                        if ($post_id) {
                            update_post_meta($post->ID, "post_id", $post_id);
                            $replace_result = $this->replace_image_in_content($content, $link, $post_id);
                            if ($replace_result["change"]) {
                                wp_update_post([
                                    "ID" => $post_id,
                                    "post_content" => $replace_result["content"]
                                ]);
                                set_post_thumbnail($post_id, $replace_result["thumbnail_id"]);
                            }
                        }
                    }

                    update_post_meta($post->ID, "status", "done");
                }
            }
        }

        public function insert_auto_news_link($args)
        {
            return wp_insert_post([
                "post_type" => "auto_news_link",
                "post_status" => "publish",
                "post_title" => $args["title"],
                "meta_input" => $args["meta_input"]
            ]);
        }

        public function insert_post($args)
        {
            return wp_insert_post([
                "post_type" => "post",
                "post_status" => "publish",
                "post_title" => $args["title"],
                "post_content" => $args["content"],
                "post_category" => $args['category'],
                "meta_input" => $args["meta_input"]
            ]);
        }

        public function get_auto_news_links_api(WP_REST_Request $request)
        {
            $this->get_auto_news_links();
            wp_send_json(["success" => true]);
        }

        public function get_auto_news_posts_api(WP_REST_Request $request)
        {
            $this->get_auto_news_posts();
            wp_send_json(["success" => true]);
        }

        public function download_img($img, $post_id)
        {
            $new_att_id = media_sideload_image($img, $post_id, "", 'id');

            if (!is_wp_error($new_att_id)) {
                return $new_att_id;
            }

            return null;
        }

        public function replace_image_in_content($content, $link, $post_id)
        {
            $doc = new Document();
            $doc->html($content);
            $nodes = $doc->find("img");
            $checked = [];
            $change = false;
            $thumbnail_id = "";
            $home_url = get_home_url();

            if ($nodes) {
                foreach ($nodes as $node) {
                    $img = $node->attr("src");

                    if (in_array($img, $checked)) {
                        break;
                    }

                    $checked[] = $img;
                    $raw_img = $img;

                    if (strpos($img, "http") === false) {
                        $tmp = parse_url($link);
                        $img = $tmp["scheme"] . "://" . $tmp["host"] . $img;
                    } else {
                        $tmp = parse_url($img);
                        $tmp2 = parse_url($home_url);
                        if ($tmp["host"] == $tmp2["host"]) {
                            break;
                        }
                    }

                    $new_att_id = $this->download_img($img, $post_id);

                    if ($new_att_id) {
                        $new_img = wp_get_attachment_url($new_att_id);
                        $content = str_replace($raw_img, $new_img, $content);
                        $change = true;
                        if (empty($thumbnail_id)) {
                            $thumbnail_id = $new_att_id;
                        }
                    }
                }
            }

            return [
                "change" => $change,
                "thumbnail_id" => $thumbnail_id,
                "content" => $content
            ];
        }
    }
endif;

function auto_news()
{
    global $auto_news;

    if (!isset($auto_news)) {
        $auto_news = new AutoNews();
        $auto_news->initialize();
    }

    return $auto_news;
}

$auto_news = auto_news();
