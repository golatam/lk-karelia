<?php

require_once(get_template_directory() . '/inc/initrk_generate_json_map.php');

function initrk_image_sizes() {
    add_theme_support('post-thumbnails');
    add_image_size('news-main-image', 884, 729, true);
    add_image_size('news-small-image', 428, 731, true);
    add_image_size('object-image', 694, 480, true);
    add_image_size('thumbnail', 150, 150, true );
}
add_action('init', 'initrk_image_sizes');

function initrk_remove_intermediate_image_sizes($sizes, $metadata) {
    $disabled_sizes = array(
        'medium',
        'medium_large',
        'large',
        '1536x1536',
        '2048x2048'
    );
    // unset disabled sizes
    foreach ($disabled_sizes as $size) {
        if (!isset($sizes[$size])) {
            continue;
        }
        unset($sizes[$size]);
    }
    return $sizes;
}
// Hook the function
add_filter('intermediate_image_sizes_advanced', 'initrk_remove_intermediate_image_sizes', 10, 2);

function create_video_post_type()
{

    $labels = array(
        'name' => __('Видео'),
        'singular_name' => __('Видео'),
        'all_items' => __('Все видео'),
        'add_new' => _x('Новое видео', 'видео'),
        'add_new_item' => __('Новое видео'),
        'edit_item' => __('Редактировать видео'),
        'new_item' => __('Новое видео'),
        'view_item' => __('Смотреть видео'),
        'search_items' => __('Искать видео'),
        'not_found' =>  __('Ничего не найдено'),
        'not_found_in_trash' => __('Нет видео'),
        'parent_item_colon' => ''
    );

    $args = array (
        'labels' => $labels,
        'public' => true,
        'has_archive' => true,
        'description' => '',
        'rewrite' => true,
        'taxonomies' => array('post_tag'),
        'query_var' => true,
        'supports'  => array('title', 'excerpt'),
        'menu_position' => 9,
        'menu_icon' => 'dashicons-video-alt3',
        'rewrite' => array('slug' => 'video')
    );

    register_post_type('video', $args);
}
add_action('init', 'create_video_post_type');

function create_points_post_type()
{
    $labels = array(
        'name' => __('Объекты'),
        'singular_name' => __('Объект'),
        'all_items' => __('Все объекты'),
        'add_new' => _x('Новый объект', 'объекты'),
        'add_new_item' => __('Новый объект'),
        'edit_item' => __('Редактировать объект'),
        'new_item' => __('Новый объект'),
        'view_item' => __('Смотреть объекты'),
        'search_items' => __('Искать объекты'),
        'not_found' =>  __('Ничего не найдено'),
        'not_found_in_trash' => __('Нет объектов'),
        'parent_item_colon' => ''
    );

    $args = array (
        'labels' => $labels,
        'public' => true,
        'has_archive' => true,
        'rewrite' => true,
        'query_var' => true,
        'editor'=> false,
        'supports'  => array('thumbnail' , 'custom-fields', 'title', 'editor'),
        'menu_position' => 7,
        'menu_icon' => 'dashicons-location-alt',
        'rewrite' => array('slug' => 'points')
    );

    register_post_type('points', $args);
}
add_action('init', 'create_points_post_type');

function initrk_remove_post_type_support() {
    remove_post_type_support( 'points', 'editor' );
}
add_action( 'init', 'initrk_remove_post_type_support' );

function create_docs_post_type()
{

    $labels = array(
        'name' => __('Документы'),
        'singular_name' => __('Документ'),
        'all_items' => __('Все документы'),
        'add_new' => _x('Новый документ', 'документы'),
        'add_new_item' => __('Новый документ'),
        'edit_item' => __('Редактировать документ'),
        'new_item' => __('Новый документ'),
        'view_item' => __('Смотреть документы'),
        'search_items' => __('Искать документы'),
        'not_found' =>  __('Ничего не найдено'),
        'not_found_in_trash' => __('Нет документов'),
        'parent_item_colon' => ''
    );

    $args = array (
        'labels' => $labels,
        'public' => true,
        'has_archive' => true,
        'rewrite' => true,
        'taxonomies' => array('post_tag'),
        'query_var' => true,
        'supports'  => array('custom-fields', 'title'),
        'menu_position' => 7,
        'menu_icon' => 'dashicons-format-aside',
        'rewrite' => array('slug' => 'docs')
    );

    register_post_type('docs', $args);
}

add_action('init', 'create_docs_post_type');

function get_sum($metakey)
{
    global $wpdb;
    $ret = "";
    $counties = $wpdb->get_col($wpdb->prepare("SELECT DISTINCT meta_value FROM $wpdb->postmeta WHERE meta_key = %s ORDER BY meta_value ASC", $metakey));
    $ret = array_sum($counties);
    return number_format($ret, 2, ',', ' ').' руб.';
}

function get_projects($metakey)
{
    global $wpdb;

    $counties = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM `".$wpdb->prefix."posts` WHERE `post_type` = %s", $metakey));
    return $counties;
}

function get_count_project($metakey)
{
    global $wpdb;
    $ret = "";
    $counties = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) meta_value FROM $wpdb->postmeta WHERE meta_value = %s", $metakey));

    return $counties;
}

function get_meta_for_filter($metakey)
{
    global $wpdb;
    $ret = "";
    $counties = $wpdb->get_col($wpdb->prepare("SELECT DISTINCT meta_value FROM $wpdb->postmeta WHERE meta_key = %s ORDER BY meta_value ASC", $metakey));
    if ($counties) {
        $ret .= "<option value=\"all-" . $metakey . "\">Выбрать все</option>";
        foreach ($counties as $county) {
            if ($_POST[$metakey] == $county) {
                $sel = "selected";
            } else {
                $sel="";
            }
            $ret .= "<option value=\"" . $county . "\" ".$sel.">" . $county . "</option>";
        }
    }
    return $ret;
}

function get_size_file($file)
{
    $bytes = filesize($file);
    $s = array('b', 'Kb', 'Mb', 'Gb');
    $e = floor(log($bytes)/log(1024));
    return sprintf('%.2f '.$s[$e], ($bytes/pow(1024, floor($e))));
}

function points_filters()
{

    if (! session_id()) {
        session_start();
    }
}
add_action('init', 'points_filters');

function replace_quotes($text)
{
    $text = htmlspecialchars_decode($text, ENT_QUOTES);
    $text = str_replace(array('«', '»'), '"', $text);
    return preg_replace_callback('/(([\"]{2,})|(?![^\W])(\"))|([^\s][\"]+(?![\w]))/u', 'replace_quotes_callback', $text);
}

function replace_quotes_callback($matches)
{
    if (count($matches) == 3) {
        return '«»';
    } elseif (!empty($matches[1])) {
        return str_replace('"', '«', $matches[1]);
    } else {
        return str_replace('"', '»', $matches[4]);
    }
}

function misha_filter_function()
{
    $args = array(
        'post_type' => 'points',
        'posts_per_page' => '-1',
        'orderby' => 'ID',
        'order' => 'ASC',
    );

    // create $args['meta_query'] array if one of the following fields is filled
    if (isset($_POST['year']) && $_POST['year'] ||
        isset($_POST['district']) && $_POST['district'] ||
        isset($_POST['project']) && $_POST['project'] ||
        isset($_POST['type_proj']) && $_POST['type_proj']) {
        $args = array(
            'post_type' => 'points',
            'posts_per_page' => '-1',
            'orderby' => 'ID',
            'order' => 'ASC',
        );
        $args['meta_query'] = array( 'relation'=>'AND' ); // AND means that all conditions of meta_query should be true
    } else {
        $args = array(
            'post_type' => 'points',
            'posts_per_page' => 'none',
            'orderby' => 'ID',
            'order' => 'ASC',
            'meta_query' => array(array('key' => '_thumbnail_id', 'compare' => 'EXISTS'))
        );
    }

    if (isset($_POST['year'])) {
        //$args = array('post_type' => 'points', 'posts_per_page' => '-1');
        if ($_POST['year'] == 'all-year') {
            $args['meta_query'][] = array(
                array(
                    'key'       => 'year',
                    'compare'   => 'EXISTS'
                )
            );
        } else {
            $args['meta_query'][] = array(
                array(
                    'key'       => 'year',
                    'value'     => $_POST['year'],
                    'compare'   => 'LIKE'
                )
            );
        }
    }

    if (isset($_POST['district'])) {
        if ($_POST['district'] == 'all-district') {
            $args['meta_query'][] = array(
                array(
                    'key'       => 'district',
                    'compare'   => 'EXISTS'
                )
            );
        } else {
            $args['meta_query'][] = array(
                array(
                    'key'       => 'district',
                    'value'     => $_POST['district'],
                    'compare'   => 'LIKE'
                )
            );
        }
    }


    if (isset($_POST['project'])) {
        if ($_POST['project'] == 'all-project') {
            $args['meta_query'][] = array(
                array(
                    'key'       => 'project',
                    'compare'   => 'EXISTS'
                )
            );
        } else {
            $args['meta_query'][] = array(
                array(
                    'key'       => 'project',
                    'value'     => $_POST['project'],
                    'compare'   => 'LIKE'
                )
            );
        }
    }

    if (isset($_POST['type_proj'])) {
        if ($_POST['type_proj'] == 'all-type_proj') {
            $args['meta_query'][] = array(
                array(
                    'key'       => 'type_proj',
                    'compare'   => 'EXISTS'
                )
            );
        } else {
            $args['meta_query'][] = array(
                array(
                    'key'       => 'type_proj',
                    'value'     => $_POST['type_proj'],
                    'compare'   => 'LIKE'
                )
            );
        }
    }

    // echo "<pre>";
    // print_R($args);
    // echo "</pre>";
    if ($args['posts_per_page'] != 'none') {
        $query = new WP_Query($args);
    }

    if ($query->have_posts()) :?>
        <div class="row gallery">
            <?php
            while ($query->have_posts()) :
                $query->the_post();
                if (get_the_post_thumbnail() == "") {
                    $class = "img-dummy";
                    $img = get_template_directory_uri()."/img/dummy2.jpg";
                } else {
                    $class = "img-wrap";
                    $img = get_the_post_thumbnail_url();
                }

                ?>
                <div class="col-lg-4 col-12 col-sm-6">
                    <a href="<?php the_permalink(); ?>">
                        <div class="<?php echo $class; ?>">
                            <img src="<?php echo $img; ?>" alt="">
                            <div class="showmore">
                                <div class="icon"></div>
                                <div class="showmore_text">Галерея объекта</div>
                            </div>
                        </div>
                        <div class="text"><?php the_title(); ?></div>
                    </a>
                </div>
            <?php endwhile; ?>
        </div>

        <?php
        wp_reset_postdata();
    else :
        echo '<div class="text">Объектов по заданным критериям не найдено!</div><div style="padding:100px;">&nbsp;</div>';
    endif;

    die();
}

add_action('wp_ajax_myfilter', 'misha_filter_function'); // wp_ajax_{ACTION HERE}
add_action('wp_ajax_nopriv_myfilter', 'misha_filter_function');



function init_rk_pagination($pages = '', $range = 2)
{
    $showitems = ($range * 2)+1;

    global $paged;
    if (empty($paged)) {
        $paged = 1;
    }

    if ($pages == '') {
        global $wp_query;
        $pages = $wp_query->max_num_pages;
        if (!$pages) {
            $pages = 1;
        }
    }

    if (1 != $pages) {
        echo '<ul class="pagination">';
        if ($paged > 2 && $paged > $range+1 && $showitems < $pages) {
            echo '<li class="pagination__item"><a class="pagination__link" href="'.get_pagenum_link(1).'">&laquo;</a></li>';
        }

        if ($paged > 1 && $showitems < $pages) {
            echo '<li class="pagination__item"><a class="pagination__link" href="'.get_pagenum_link($paged - 1).'">&lsaquo;</a></li>';
        }

        for ($i=1; $i <= $pages; $i++) {
            if (1 != $pages &&( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems )) {
                echo ($paged == $i) ? '<li class="pagination__item current"><a href="#" rel="nofollow" class="pagination__link">'.$i.'</a></li>':'<li class="pagination__item"><a class="pagination__link" href="'.get_pagenum_link($i).'">'.$i.'</a></li>';
            }
        }

        if ($paged < $pages && $showitems < $pages) {
            echo '<li class="pagination__item"><a class="pagination__link" href="'.get_pagenum_link($paged + 1).'">&rsaquo;</a></li>';
        }
        if ($paged < $pages-1 &&  $paged+$range-1 < $pages && $showitems < $pages) {
            echo '<li class="pagination__item"><a class="pagination__link" href="'.get_pagenum_link($pages).'">&raquo;</a></li>';
        }
        echo "</ul>\n";
    }
}

add_filter('rest_points_query', function ($args) {
    $filters = ['relation' => 'AND'];
    if (isset($_GET['email'])) {
        $filter = [
            'key' => 'email',
            'vaue' => $_GET['email'],
            'compare' => '=',
        ];
        array_push($filters, $filter);
    }
    $args['meta_query'] = $filters;
    return $args;
});


