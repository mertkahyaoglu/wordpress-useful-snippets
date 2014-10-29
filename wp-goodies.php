<!--Template Directory-->
<?php bloginfo('template_directory'); ?>/


<?php //post thumbnail
$thumb = wp_get_attachment_image_src( get_post_thumbnail_id($araclar->ID), 'large'); 
if($thumb) {?>
<a href="<?=$link?>"><img src="<?=$thumb[0]?>" class="img-responsive"></a>
<?php } ?>


<?php //get taxonomy terms
$terms = get_terms("turtipi");
if ( !empty( $terms ) && !is_wp_error( $terms ) ){
 echo "<ul>";
 foreach ( $terms as $term ) {
   echo "<li>" . $term->name . "</li>";  
 }
 echo "</ul>";
}
?>

<?php
define('WP_DEBUG', false);
define( 'WP_AUTO_UPDATE_CORE', false );
define( 'AUTOMATIC_UPDATER_DISABLED', true ); 
?>

<?php 
// get posts by taxonomy                       
$posts = get_posts(array(
    'showposts' => -1,
    'post_type' => 'turlar',
    'tax_query' => array(
        array(
            'taxonomy' => 'turtipi',
            'field' => 'slug',
            'terms' => $term->slug
        )
    ),
    'orderby' => 'id',
    'order' => 'ASC')
);
?>

<?php 
// get post term by id
$args = array('orderby' => 'id', 'order' => 'ASC');
$terms = wp_get_post_terms( $page_id, "turtipi", $args ); 
?>


<?php
//disable notification
add_filter( 'pre_site_transient_update_plugins', create_function( '$a', "return null;" ) );
//disable delete pages
$role = get_role('administrator');
$role->remove_cap('delete_pages');
$role->remove_cap('delete_others_pages');
$role->remove_cap('delete_published_pages');
?>



<?php
add_theme_support('post-thumbnails');
add_image_size('slides', 960, 400, true); //crop true
?>

//import files
<?php get_template_part('banner', 'home'); //banner-home.php ?>

<?php is_front_page() //home page ?>
<?php the_field('year'); ?>

<!--Ana Sayfa URL-->
<?php echo home_url(); ?>

<!-- title a göre link vermek -->
<a href=”<?php echo esc_url( get_permalink(get_page_by_title('Green Nature Diamond') ) ); ?>”>deneme</a>
Green Nature Diamond Yerine o sayfanin title’ini yaziyorsun... 

<!-- Check Page Language -->
<?php if(ICL_LANGUAGE_CODE=='en'){ ?>

<?php } if(ICL_LANGUAGE_CODE=='tr'){ ?>

<?php } ?>

<?php
// admin login page logo
function my_login_logo() { ?>
    <style type="text/css">
        body.login div#login h1 a {
            background-image: url(<?php echo get_stylesheet_directory_uri(); ?>/img/logo1.png);
            padding-bottom: 30px;
        }
    </style>
<?php }
add_action( 'login_enqueue_scripts', 'my_login_logo' );
function my_login_logo_url() {
    return home_url();
}
add_filter( 'login_headerurl', 'my_login_logo_url' );

function my_login_logo_url_title() {
    return 'Allout Door';
}
add_filter( 'login_headertitle', 'my_login_logo_url_title' );

?>

<?php // Menu
$menu_name = 'menu1';
if (($menu = wp_get_nav_menu_object($menu_name) ) && ( isset($menu) )) {
    $menuitems = wp_get_nav_menu_items($menu->term_id);
    $count = 0;
    $submenu = false;
    foreach ($menuitems as $item):
        $id = get_post_meta($item->ID, '_menu_item_object_id', true);
        $page = get_page($id);
        $link = get_page_link($id);
        if (!$item->menu_item_parent): $parent_id = $item->ID;?>
            <li>
                <a href="<?php echo $link; ?>"><?php echo $page->post_title; ?></a>
        <?php endif; ?>
            <?php if ($parent_id == $item->menu_item_parent): ?>
                <?php if (!$submenu): $submenu = true; ?>
                    <ul>
                <?php endif; ?>
                    <li>
                        <a href="<?php echo $link; ?>"><?php echo $page->post_title; ?></a>
                    </li>
            <?php if ($menuitems[$count + 1]->menu_item_parent != $parent_id && $submenu): ?>
                    </ul>
                        <?php $submenu = false;
                    endif; ?>

            <?php endif; ?>

            <?php if ($menuitems[$count + 1]->menu_item_parent != $parent_id): ?>
            </li>                           
            <?php $submenu = false;
        endif; ?>
        <?php $count++;
    endforeach; ?>
<?php } ?>

<!--guest book-->
<?php 
// Disable support for comments and trackbacks in post types
function df_disable_comments_post_types_support() {
  $post_types = get_post_types();
  foreach ($post_types as $post_type) {
    if(post_type_supports($post_type, 'comments')) {
      remove_post_type_support($post_type, 'comments');
      remove_post_type_support($post_type, 'trackbacks');
    }
  }
}
add_action('admin_init', 'df_disable_comments_post_types_support');

// Close comments on the front-end
function df_disable_comments_status() {
  return false;
}
add_filter('comments_open', 'df_disable_comments_status', 20, 2);
add_filter('pings_open', 'df_disable_comments_status', 20, 2);

// Hide existing comments
function df_disable_comments_hide_existing_comments($comments) {
  $comments = array();
  return $comments;
}
add_filter('comments_array', 'df_disable_comments_hide_existing_comments', 10, 2);

// Remove comments page in menu
function df_disable_comments_admin_menu() {
  remove_menu_page('edit-comments.php');
}
add_action('admin_menu', 'df_disable_comments_admin_menu');

// Redirect any user trying to access comments page
function df_disable_comments_admin_menu_redirect() {
  global $pagenow;
  if ($pagenow === 'edit-comments.php') {
    wp_redirect(admin_url()); exit;
  }
}
add_action('admin_init', 'df_disable_comments_admin_menu_redirect');

// Remove comments metabox from dashboard
function df_disable_comments_dashboard() {
  remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');
}
add_action('admin_init', 'df_disable_comments_dashboard');

// Remove comments links from admin bar
function df_disable_comments_admin_bar() {
  if (is_admin_bar_showing()) {
    remove_action('admin_bar_menu', 'wp_admin_bar_comments_menu', 60);
  }
}
add_action('init', 'df_disable_comments_admin_bar');
?>
