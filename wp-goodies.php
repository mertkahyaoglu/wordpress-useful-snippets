<?php bloginfo('template_directory'); ?>/

<?php //language check
if(substr(get_bloginfo( 'language' ), 0, 2)=='en'){ ?>

<?php register_nav_menu( 'primary', __( 'menu1', 'site' ) );?>

<?php //get menu items
$menu_name = 'mymenu';
if (($menu = wp_get_nav_menu_object($menu_name) ) && ( isset($menu) )) {
  $menuitems = wp_get_nav_menu_items($menu->term_id);
  foreach ($menuitems as $item):?>
    <li><a href="<?=$item->url?>"><?php echo $item->title; ?></a></li>
  <?php endforeach; ?>
<?php } ?>

<?php // remove admin menus
add_action( 'admin_menu', 'my_remove_menu_pages' );
function my_remove_menu_pages() {
	remove_menu_page('tools.php');
  remove_menu_page('upload.php');
}
?>

<?php //main page attributes
wp_reset_query();
$page_id = get_the_ID();
$page_data = get_page( $page_id );
$title = $page_data->post_title;
$content = apply_filters('the_content', $page_data->post_content);
?>

<?php //TITLE
wp_title('|',true,'right'); ?><?php bloginfo('name'); ?>

<?php //slider post
$banner = get_post(8);
$images = get_post_meta($banner->ID, 'vdw_gallery_id', true);
if ($images)
foreach ($images as $image) {
$img4 = wp_get_attachment_image_src($image,'full');
$alt = get_post_meta($image, '_wp_attachment_image_alt', true);
?>
<div class="ls-layer">
    <img src="<?=$img4[0]?>" class="ls-bg" alt="Crazy Daisy">
    <h1 class="ls-s1" style="left: 20px !important; top: 450px;font-size:28px;color:#fff;font-family:ubuntu"><?=$alt?></h1>
</div>
<?php } ?>

<?php //images
$images = get_post_meta($page_id, 'vdw_gallery_id', true);
if ($images)
foreach ($images as $image) {
$img = wp_get_attachment_image_src($image,'full');
$alt = get_post_meta($image, '_wp_attachment_image_alt', true);
?>

<?php } ?>

<?php //post thumbnail
$thumb = wp_get_attachment_image_src( get_post_thumbnail_id($araclar->ID), 'large');
if($thumb) {?>
<a href="<?=$link?>"><img src="<?=$thumb[0]?>" class="img-responsive"></a>
<?php } ?>


<?php // get post by slug
$post = get_page_by_path('yerli-urunler');
?>

<?php // get post by category
$args = array(
    'showposts' => 4,
    'tax_query' => array(
        array(
            'post_type' => "yacht",
            'taxonomy' => "cat-yacht",
            'field' => 'slug',
            'terms' => array(
                "sailing-yacht"
            ),
        )
    )
);

$my_query = new WP_Query($args);
if( $my_query->have_posts() ) {
  while ($my_query->have_posts()) : $my_query->the_post();
  $cat = get_the_terms(get_the_ID(), "cat-yacht")[0];?>

<?php endwhile; } wp_reset_query();?>


<?php //custom post columns
add_filter("manage_edit-gece_columns", "gece_edit_columns");

function gece_edit_columns($columns){
  $columns = array(
    "cb" => "&lt;input type='checkbox' />",
    "title" => __( 'Gece Aktivitesi' ),
    "date" => "Tarih"
  );

  return $columns;
}

add_action( 'manage_odemeler_posts_custom_column' , 'custom_odemeler_column', 10, 2 );
function custom_odemeler_column( $column, $post_id ) {
    switch ( $column ) {
        case 'content' :
            $content = get_post_meta($post_id, "detay", true);
            echo $content;
            break;
    }
}
?>

<?php // get posts by taxonomy
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

<?php // CUSTOM POSTS
$args = array(
  'post_type' => "haber",
  'post_status' => 'publish',
  'posts_per_page' => -1,
  'orderby' => "menu_order",
  'order' => "ASC"
);

$my_query = new WP_Query($args);
if( $my_query->have_posts() ) {
  while ($my_query->have_posts()) : $my_query->the_post();
    $thumb = wp_get_attachment_image_src( get_post_thumbnail_id(get_the_ID()), 'full'); ?>
    <a href="<?= get_post_permalink(); ?>" data-img="<?=$thumb[0]?>">
      <p style="background-color:#BACBFF"><?php the_title(); ?></p>
    </a>
  <?php
  endwhile;
}
wp_reset_query();
?>

<?php // get taxonomy tree
function get_taxonomy_hierarchy( $taxonomy, $parent = 0 ) {
  $taxonomy = is_array( $taxonomy ) ? array_shift( $taxonomy ) : $taxonomy;
  $terms = get_terms( $taxonomy, array( 'parent' => $parent ) );
  $children = array();
  foreach ( $terms as $term ){
    $term->children = get_taxonomy_hierarchy( $taxonomy, $term->term_id );
    $children[ $term->term_id ] = $term;
  }
  return $children;
}
// usage : $hierarchy = get_taxonomy_hierarchy( 'category' );
?>

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


//add custom post from form
<div id="success" class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="margin-top:10px">
  <div class='alert alert-success' id='success'>
    <a class='close' data-dismiss='alert'>×</a>
    <strong>Thank You!</strong><br/>Your Comment Has Been Send
  </div>
</div>

  <?php
  $metin = $_POST['metin'];
  $my_post = array(
    'post_title'    => $baslik,
    'post_content'  => sanitize_text_field($metin),
    'post_author'   => 1,
    'post_type' => 'guest_book',
    'post_status'   => 'publish',
    'comment_status' => 'closed',
    'ping_status' => 'closed',
    'post_date' => current_time('mysql'),
    'post_date_gmt' => current_time('mysql'),
    'post_modified_gmt' => current_time('mysql')
  );

  $post_id = wp_insert_post($my_post);
  add_post_meta($post_id, 'turadi', $turlar_title, true);
  add_post_meta($post_id, 'customer_name', $customer_name, true);
  add_post_meta($post_id, 'email', $email, true);
  add_post_meta($post_id, 'onay', "0", true);
?>

<?php // load attachment from front-end
require_once( ABSPATH . 'wp-admin/includes/image.php' );
require_once( ABSPATH . 'wp-admin/includes/file.php' );
require_once( ABSPATH . 'wp-admin/includes/media.php' );

$attach_id = media_handle_upload( 'upload', $post_id );
add_post_meta($post_id, 'cv_pdf', $attach_id, true);
?>
<a target="_blank" href="<?=wp_get_attachment_url($cv_pdf)?>">Cv için tıklayınız</a>


<?php //mailpoet front-end form process
function processMyForm(){

    //you could make some validation even though validation
    //is also processed in our function
    $my_email_variable = $_POST['my_email_variable'] ;
    $my_list_id1 = $_POST['my_list_id1'] ;
    $my_list_id2 = $_POST['my_list_id2'] ;

    //in this array firstname and lastname are optional
    $user_data = array(
        'email' => $my_email_variable,
        'firstname' => $firstname,
        'lastname' => $lastname);

    $data_subscriber = array(
      'user' => $user_data,
      'user_list' => array('list_ids' => array($my_list_id1,$my_list_id2))
    );

    $helper_user = WYSIJA::get('user','helper');
    $helper_user->addSubscriber($data_subscriber);
}

//initialize this function only when your subscription form data has been posted
add_action('init','processMyForm') ;,
?>

<?php // custom metabox
add_action( 'add_meta_boxes', 'cb_metabox_ithal' );
function cb_metabox_ithal() {
  add_meta_box( 'general', 'Ürün Detayları', 'cb_metabox_content_ithal', 'ithal', 'normal', 'high' );
}

function cb_metabox_content_ithal() {
  global $post;
  $values = get_post_custom( $post->ID );
  $urun_yili = $values["urun_yili"][0];

  wp_nonce_field( 'my_nonce_i', 'nonce_i' );
  ?>
  <style type="text/css">
  .kapsayici {float:left;width:100%;min-height:30px;margin-top: 10px}
  .bolum_1 {float:left;width:20%;min-height:30px;}
  .bolum_2 {float:left;width:75%;min-height:30px;}
  .bolum_3 {float:left;width:5%;min-height:30px;text-align:center}
  </style>

  <div class="kapsayici" style="clear:both">

    <div class="bolum_1">Ürün Yılı</div>
    <div class="bolum_3">:</div>
    <div class="bolum_2">
      <input type="text" placeholder="Yıl" name="urun_yili" id="urun_yili"  value="<?= $urun_yili ?>" />
    </div>

  </div>

<?php }
//save post
add_action( 'save_post', 'cb_metabox_ithal_save' );
function cb_metabox_ithal_save( $post_id ) {
  $slug = "ithal";
  if($slug != $_POST['post_type']) return;
  if(defined( 'DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;
  if(!isset( $_POST['nonce_i']) || !wp_verify_nonce($_POST['nonce_i'], 'my_nonce_i')) return;
  if(!current_user_can( 'edit_post') ) return;

  if(isset($_POST['ulke'])) update_post_meta($post_id, 'ulke', $_POST['ulke']);

  $chk = isset( $_POST['stok']) && $_POST['stok'] ? '1' : '0';
  update_post_meta($post_id, 'stok', $chk);
}
?>
