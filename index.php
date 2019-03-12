<?php 
/*
 Plugin Name: VW Dentist Pro Posttype
 lugin URI: https://www.vwthemes.com/
 Description: Creating new post type for VW Dentist Pro Theme.
 Author: VW Themes
 Version: 1.0
 Author URI: https://www.vwthemes.com/
*/

define( 'VW_DENTIST_PRO_POSTTYPE_VERSION', '1.0' );
add_action( 'init', 'vw_dentist_pro_posttype_create_post_type' );

function vw_dentist_pro_posttype_create_post_type() {

  register_post_type( 'services',
    array(
        'labels' => array(
            'name' => __( 'Services','vw-dentist-pro-posttype' ),
            'singular_name' => __( 'Services','vw-dentist-pro-posttype' )
        ),
        'capability_type' =>  'post',
        'menu_icon'  => 'dashicons-tag',
        'public' => true,
        'supports' => array(
        'title',
        'editor',
        'thumbnail',
        'page-attributes',
        'comments'
        )
    )
  );
  
  register_post_type( 'testimonials',
    array(
      'labels' => array(
        'name' => __( 'Testimonial','vw-dentist-pro-posttype' ),
        'singular_name' => __( 'Testimonial','vw-dentist-pro-posttype' )
      ),
      'capability_type' => 'post',
      'menu_icon'  => 'dashicons-businessman',
      'public' => true,
      'supports' => array(
        'title',
        'editor',
        'thumbnail'
      )
    )
  );
  register_post_type( 'doctors',
    array(
      'labels' => array(
        'name' => __( 'Doctor','vw-dentist-pro-posttype' ),
        'singular_name' => __( 'Doctor','vw-dentist-pro-posttype' )
      ),
        'capability_type' => 'post',
        'menu_icon'  => 'dashicons-businessman',
        'public' => true,
        'supports' => array( 
          'title',
          'editor',
          'thumbnail'
      )
    )
  );
}

/* ----------------- Services --------------------- */

function vw_dentist_pro_posttype_images_metabox_enqueue($hook) {
  if ( 'post.php' === $hook || 'post-new.php' === $hook ) {
    wp_enqueue_script('vw-dentist-pro-posttype-images-metabox', plugin_dir_url( __FILE__ ) . '/js/img-metabox.js', array('jquery', 'jquery-ui-sortable'));

    global $post;
    if ( $post ) {
      wp_enqueue_media( array(
          'post' => $post->ID,
        )
      );
    }

  }
}
add_action('admin_enqueue_scripts', 'vw_dentist_pro_posttype_images_metabox_enqueue');
// Services Meta
function vw_dentist_pro_posttype_bn_custom_meta_services() {

    add_meta_box( 'bn_meta', __( 'Services Meta', 'vw-dentist-pro-posttype' ), 'vw_dentist_pro_posttype_bn_meta_callback_services', 'services', 'normal', 'high' );
}
/* Hook things in for admin*/
if (is_admin()){
  add_action('admin_menu', 'vw_dentist_pro_posttype_bn_custom_meta_services');
}

function vw_dentist_pro_posttype_bn_meta_callback_services( $post ) {
    wp_nonce_field( basename( __FILE__ ), 'bn_nonce' );
    $bn_stored_meta = get_post_meta( $post->ID );
    $service_icon = get_post_meta( $post->ID, 'meta-image', true );
    ?>
  <div id="property_stuff">
    <table id="list-table">     
      <tbody id="the-list" data-wp-lists="list:meta">
        <tr id="meta-1">
          <p>
            <label for="meta-image"><?php echo esc_html('Icon Image'); ?></label><br>
            <input type="text" name="meta-image" id="meta-image" class="meta-image regular-text" value="<?php echo esc_html($service_icon); ?>">
            <input type="button" class="button image-upload" value="Browse">
          </p>
          <div class="image-preview"><img src="<?php echo ($service_icon); ?>" style="max-width: 250px;"></div>
        </tr>
        
      </tbody>
    </table>
  </div>
  <?php
}

function vw_dentist_pro_posttype_bn_meta_save_services( $post_id ) {



  if (!isset($_POST['bn_nonce']) || !wp_verify_nonce($_POST['bn_nonce'], basename(__FILE__))) {
    return;
  }

  if (!current_user_can('edit_post', $post_id)) {
    return;
  }

  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
    return;
  }
  // Save Image
  if( isset( $_POST[ 'meta-image' ] ) ) {
      update_post_meta( $post_id, 'meta-image', esc_url_raw($_POST[ 'meta-image' ]) );
  }
  if( isset( $_POST[ 'meta-url' ] ) ) {
      update_post_meta( $post_id, 'meta-url', esc_url_raw($_POST[ 'meta-url' ]) );
  }
}
add_action( 'save_post', 'vw_dentist_pro_posttype_bn_meta_save_services' );

/* Services shortcode */
function vw_dentist_pro_posttype_services_func( $atts ) {

  $services = '';
  $services = '<div class="row">';
  $query = new WP_Query( array( 'post_type' => 'services') );

    if ( $query->have_posts() ) :

  $k=1;
  $new = new WP_Query('post_type=services');

  while ($new->have_posts()) : $new->the_post();
        $custom_url ='';
        $post_id = get_the_ID();
        $services_icon= get_post_meta($post_id,'meta-image',true);
        $thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post_id), 'large' );
        if(has_post_thumbnail()) { $thumb_url = $thumb['0']; }
        $url = $thumb['0'];
        $excerpt = wp_trim_words(get_the_excerpt(),15);
        $custom_url = get_permalink();
        $services .= '<div class="col-lg-4 col-md-6 col-sm-6 services-box">
                        <div class="services_content">
                          <div class="services-title">
                           <img src="'.esc_url($services_icon).'" class="pra-img">
                            <h4><a href="'.esc_url($custom_url).'">'.esc_html(get_the_title()) .'</a></h4>
                            <div class="services-info">
                              '.$excerpt.'
                            </div>
                          </div>
                        </div>
                      </div>';


    if($k%2 == 0){
      $services.= '<div class="clearfix"></div>';
    }
      $k++;
  endwhile;
  else :
    $services = '<h2 class="center">'.esc_html__('Post Not Found','vw-dentist-pro-posttype').'</h2>';
  endif;
  $services .= '</div>';
  return $services;
}

add_shortcode( 'vw-dentist-pro-services', 'vw_dentist_pro_posttype_services_func' );


/*---------------------------------- Testimonial section -------------------------------------*/
/* Adds a meta box to the Testimonial editing screen */
function vw_dentist_pro_posttype_bn_testimonial_meta_box() {
  add_meta_box( 'vw-dentist-pro-posttype-testimonial-meta', __( 'Enter Details', 'vw-dentist-pro-posttype' ), 'vw_dentist_pro_posttype_bn_testimonial_meta_callback', 'testimonials', 'normal', 'high' );
}
// Hook things in for admin
if (is_admin()){
    add_action('admin_menu', 'vw_dentist_pro_posttype_bn_testimonial_meta_box');
}

/* Adds a meta box for custom post */
function vw_dentist_pro_posttype_bn_testimonial_meta_callback( $post ) {
  wp_nonce_field( basename( __FILE__ ), 'vw_dentist_pro_posttype_posttype_testimonial_meta_nonce' );
  $bn_stored_meta = get_post_meta( $post->ID );
  $desigstory = get_post_meta( $post->ID, 'vw_dentist_pro_posttype_testimonial_desigstory', true );
  $test_facebook = get_post_meta( $post->ID, 'meta-tes-facebookurl', true );
  $test_linkedin = get_post_meta( $post->ID, 'meta-tes-linkdenurl', true );
  $test_twitter = get_post_meta( $post->ID, 'meta-tes-twitterurl', true );
  $test_gplus = get_post_meta( $post->ID, 'meta-tes-googleplusurl', true );
  $test_instagram = get_post_meta( $post->ID, 'meta-tes-instagram', true );
  $test_pinterest = get_post_meta( $post->ID, 'meta-tes-pinterest', true );
  ?>
  <div id="testimonials_custom_stuff">
    <table id="list">
      <tbody id="the-list" data-wp-lists="list:meta">
        <tr id="meta-1">
          <td class="left">
            <?php _e( 'Designation', 'vw-dentist-pro-posttype' )?>
          </td>
          <td class="left" >
            <input type="text" name="vw_dentist_pro_posttype_testimonial_desigstory" id="vw_dentist_pro_posttype_testimonial_desigstory" value="<?php echo esc_attr( $desigstory ); ?>" />
          </td>
        </tr>
        <tr id="meta-2">
          <td class="left">
            <?php _e( 'Facebook Url', 'vw-dentist-pro-posttype' )?>
          </td>
          <td class="left" >
            <input type="url" name="meta-tes-facebookurl" id="meta-tes-facebookurl" value="<?php echo esc_html($test_facebook); ?>" />
          </td>
        </tr>
        <tr id="meta-3">
          <td class="left">
            <?php _e( 'Linkedin Url', 'vw-dentist-pro-posttype' )?>
          </td>
          <td class="left" >
            <input type="url" name="meta-tes-linkdenurl" id="meta-tes-linkdenurl" value="<?php echo esc_html($test_linkedin); ?>" />
          </td>
        </tr>
        <tr id="meta-4">
          <td class="left">
            <?php _e( 'Twitter Url', 'vw-dentist-pro-posttype' ); ?>
          </td>
          <td class="left" >
            <input type="url" name="meta-tes-twitterurl" id="meta-tes-twitterurl" value="<?php echo esc_html($test_twitter); ?>" />
          </td>
        </tr>
        <tr id="meta-5">
          <td class="left">
            <?php _e( 'GooglePlus Url', 'vw-dentist-pro-posttype' ); ?>
          </td>
          <td class="left" >
            <input type="url" name="meta-tes-googleplusurl" id="meta-tes-googleplusurl" value="<?php echo esc_html($test_gplus); ?>" />
          </td>
        </tr>
        <tr id="meta-6">
          <td class="left">
            <?php _e( 'Instagram Url', 'vw-dentist-pro-posttype' ); ?>
          </td>
          <td class="left" >
            <input type="url" name="meta-tes-instagram" id="meta-tes-instagram" value="<?php echo esc_html($test_instagram); ?>" />
          </td>
        </tr>
        <tr id="meta-7">
          <td class="left">
            <?php _e( 'Pinterest Url', 'vw-dentist-pro-posttype' ); ?>
          </td>
          <td class="left" >
            <input type="url" name="meta-tes-pinterest" id="meta-tes-pinterest" value="<?php echo esc_html($test_pinterest); ?>" />
          </td>
        </tr>
      </tbody>
    </table>
  </div>
  <?php
}

/* Saves the custom meta input */
function vw_dentist_pro_posttype_bn_metadesig_save( $post_id ) {
  if (!isset($_POST['vw_dentist_pro_posttype_posttype_testimonial_meta_nonce']) || !wp_verify_nonce($_POST['vw_dentist_pro_posttype_posttype_testimonial_meta_nonce'], basename(__FILE__))) {
    return;
  }

  if (!current_user_can('edit_post', $post_id)) {
    return;
  }

  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
    return;
  }

  // Save desig.
  if( isset( $_POST[ 'vw_dentist_pro_posttype_testimonial_desigstory' ] ) ) {
    update_post_meta( $post_id, 'vw_dentist_pro_posttype_testimonial_desigstory', sanitize_text_field($_POST[ 'vw_dentist_pro_posttype_testimonial_desigstory']) );
  }
  // Save facebookurl
  if( isset( $_POST[ 'meta-tes-facebookurl' ] ) ) {
      update_post_meta( $post_id, 'meta-tes-facebookurl', esc_url($_POST[ 'meta-tes-facebookurl' ]) );
  }
  // Save linkdenurl
  if( isset( $_POST[ 'meta-tes-linkdenurl' ] ) ) {
      update_post_meta( $post_id, 'meta-tes-linkdenurl', esc_url($_POST[ 'meta-tes-linkdenurl' ]) );
  }
  if( isset( $_POST[ 'meta-tes-twitterurl' ] ) ) {
      update_post_meta( $post_id, 'meta-tes-twitterurl', esc_url($_POST[ 'meta-tes-twitterurl' ]) );
  }
  // Save googleplusurl
  if( isset( $_POST[ 'meta-tes-googleplusurl' ] ) ) {
      update_post_meta( $post_id, 'meta-tes-googleplusurl', esc_url($_POST[ 'meta-tes-googleplusurl' ]) );
  }

  // Save Instagram
  if( isset( $_POST[ 'meta-tes-instagram' ] ) ) {
      update_post_meta( $post_id, 'meta-tes-instagram', esc_url($_POST[ 'meta-tes-instagram' ]) );
  }
  // Save Pinterest
  if( isset( $_POST[ 'meta-tes-pinterest' ] ) ) {
      update_post_meta( $post_id, 'meta-tes-pinterest', esc_url($_POST[ 'meta-tes-pinterest' ]) );
  }

}

add_action( 'save_post', 'vw_dentist_pro_posttype_bn_metadesig_save' );

/*---------------------------------- testimonials shortcode --------------------------------------*/
function vw_dentist_pro_posttype_testimonial_func( $atts ) {
  $testimonial = '';
  $testimonial = '<div class="row all-testimonial">';
  $query = new WP_Query( array( 'post_type' => 'testimonials') );

    if ( $query->have_posts() ) :

  $k=1;
  $new = new WP_Query('post_type=testimonials');
  while ($new->have_posts()) : $new->the_post();

        $post_id = get_the_ID();
         $thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post_id), 'large' );
        if(has_post_thumbnail()) { $thumb_url = $thumb['0']; }
        $url = $thumb['0'];
        $custom_url ='';
        
        $excerpt = wp_trim_words(get_the_excerpt(),15);
        $tdegignation= get_post_meta($post_id,'vw_dentist_pro_posttype_testimonial_desigstory',true);
        if(get_post_meta($post_id,'meta-testimonial-url',true !='')){$custom_url =get_post_meta($post_id,'meta-testimonial-url',true); } else{ $custom_url = get_permalink(); }
        $testimonial .= '

            <div class="our_testimonial_outer col-lg-4 col-md-6 col-sm-6">
              <div class="testimonial_inner">
                <div class="row hover_border">
                  <div class="col-md-12">
                     <img class="classes-img" src="'.esc_url($thumb_url).'" alt="attorney-thumbnail" />
                    <h4><a href="'.esc_url($custom_url).'">'.esc_html(get_the_title()) .'</a></h4>
                    <div class="tdesig">'.$tdegignation.'</div>
                    <div class="short_text">'.$excerpt.'</div>
                  </div>
                </div>
              </div>
            </div>';
    if($k%2 == 0){
      $testimonial.= '<div class="clearfix"></div>';
    }
      $k++;
  endwhile;
  else :
    $testimonial = '<h2 class="center">'.esc_html__('Post Not Found','vw_dentist_pro_posttype').'</h2>';
  endif;
  return $testimonial;
}

add_shortcode( 'vw-dentist-pro-testimonials', 'vw_dentist_pro_posttype_testimonial_func' );

/*-------------------------------------- Teacher-------------------------------------------*/
/* Adds a meta box for Designation */
function vw_dentist_pro_posttype_bn_doctors_meta() {
    add_meta_box( 'vw_dentist_pro_posttype_bn_meta', __( 'Enter Details','vw-dentist-pro-posttype' ), 'vw_dentist_pro_posttype_ex_bn_meta_callback', 'doctors', 'normal', 'high' );
}
// Hook things in for admin
if (is_admin()){
    add_action('admin_menu', 'vw_dentist_pro_posttype_bn_doctors_meta');
}
/* Adds a meta box for custom post */
function vw_dentist_pro_posttype_ex_bn_meta_callback( $post ) {
    wp_nonce_field( basename( __FILE__ ), 'vw_dentist_pro_posttype_bn_nonce' );
    $bn_stored_meta = get_post_meta( $post->ID );
    $teacher_email = get_post_meta( $post->ID, 'meta-doctors-email', true );
    $teacher_phone = get_post_meta( $post->ID, 'meta-doctors-phone', true );
    $teacher_facebook = get_post_meta( $post->ID, 'meta-tfacebookurl', true );
    $teacher_linkedin = get_post_meta( $post->ID, 'meta-tlinkdenurl', true );
    $teacher_twitter = get_post_meta( $post->ID, 'meta-ttwitterurl', true );
    $teacher_gplus = get_post_meta( $post->ID, 'meta-tgoogleplusurl', true );
    $teacher_desig = get_post_meta( $post->ID, 'meta-designation', true );
    $teacher_instagram = get_post_meta( $post->ID, 'meta-tinstagram', true );
    $teacher_pinterest = get_post_meta( $post->ID, 'meta-pinterest', true );
    $teacher_speciality = get_post_meta( $post->ID, 'meta-doctor-speciality', true );
    $teacher_percentage = get_post_meta( $post->ID, 'meta-speciality-per', true );
    ?>
  
    <div id="agent_custom_stuff">
        <table id="list-table">         
            <tbody id="the-list" data-wp-lists="list:meta">
                <tr id="meta-1">
                  <td class="left">
                      <?php _e( 'Email', 'vw-dentist-pro-posttype' )?>
                  </td>
                  <td class="left" >
                      <input type="text" name="meta-doctors-email" id="meta-doctors-email" value="<?php echo esc_html($teacher_email); ?>" />
                  </td>
                </tr>
                <tr id="meta-1">
                  <td class="left">
                      <?php _e( 'Phone', 'vw-dentist-pro-posttype' )?>
                  </td>
                  <td class="left" >
                      <input type="text" name="meta-doctors-phone" id="meta-doctors-phone" value="<?php echo esc_html($teacher_phone); ?>" />
                  </td>
                </tr>
                <tr id="meta-3">
                  <td class="left">
                    <?php _e( 'Facebook Url', 'vw-dentist-pro-posttype' )?>
                  </td>
                  <td class="left" >
                    <input type="url" name="meta-tfacebookurl" id="meta-tfacebookurl" value="<?php echo esc_html($teacher_facebook); ?>" />
                  </td>
                </tr>
                <tr id="meta-4">
                  <td class="left">
                    <?php _e( 'Linkedin Url', 'vw-dentist-pro-posttype' )?>
                  </td>
                  <td class="left" >
                    <input type="url" name="meta-tlinkdenurl" id="meta-tlinkdenurl" value="<?php echo esc_html($teacher_linkedin); ?>" />
                  </td>
                </tr>
                <tr id="meta-5">
                  <td class="left">
                    <?php _e( 'Twitter Url', 'vw-dentist-pro-posttype' ); ?>
                  </td>
                  <td class="left" >
                    <input type="url" name="meta-ttwitterurl" id="meta-ttwitterurl" value="<?php echo esc_html($teacher_twitter); ?>" />
                  </td>
                </tr>
                <tr id="meta-6">
                  <td class="left">
                    <?php _e( 'GooglePlus Url', 'vw-dentist-pro-posttype' ); ?>
                  </td>
                  <td class="left" >
                    <input type="url" name="meta-tgoogleplusurl" id="meta-tgoogleplusurl" value="<?php echo esc_html($teacher_gplus); ?>" />
                  </td>
                </tr>
                <tr id="meta-7">
                  <td class="left">
                    <?php _e( 'Instagram Url', 'vw-dentist-pro-posttype' ); ?>
                  </td>
                  <td class="left" >
                    <input type="url" name="meta-tinstagram" id="meta-tinstagram" value="<?php echo esc_html($teacher_instagram); ?>" />
                  </td>
                </tr>
                <tr id="meta-8">
                  <td class="left">
                    <?php _e( 'Pinterest Url', 'vw-dentist-pro-posttype' ); ?>
                  </td>
                  <td class="left" >
                    <input type="url" name="meta-pinterest" id="meta-pinterest" value="<?php echo esc_html($teacher_pinterest); ?>" />
                  </td>
                </tr>
                <tr id="meta-9">
                  <td class="left">
                    <?php _e( 'Designation', 'vw-dentist-pro-posttype' ); ?>
                  </td>
                  <td class="left" >
                    <input type="text" name="meta-designation" id="meta-designation" value="<?php echo esc_html($teacher_desig); ?>" />
                  </td>
                </tr>
                <tr id="meta-10">
                  <td class="left">
                    <?php _e( 'Speciality', 'vw-dentist-pro-posttype' ); ?>
                  </td>
                  <td class="left" >
                    <input type="text" name="meta-doctor-speciality" id="meta-doctor-speciality" value="<?php echo esc_html($teacher_speciality); ?>" />
                  </td>
                </tr>
                <tr id="meta-10">
                  <td class="left">
                    <?php _e( 'Speciality Percentage', 'vw-dentist-pro-posttype' ); ?>
                  </td>
                  <td class="left" >
                    <input type="text" name="meta-speciality-per" id="meta-speciality-per" value="<?php echo esc_html($teacher_percentage); ?>" />
                  </td>
                </tr>
            </tbody>
        </table>
    </div>
    <?php
}
/* Saves the custom Designation meta input */
function vw_dentist_pro_posttype_ex_bn_metadesig_save( $post_id ) {

  
    if( isset( $_POST[ 'meta-doctors-email' ] ) ) {
        update_post_meta( $post_id, 'meta-doctors-email', esc_html($_POST[ 'meta-doctors-email' ]) );
    }
    if( isset( $_POST[ 'meta-doctors-phone' ] ) ) {
        update_post_meta( $post_id, 'meta-doctors-phone', esc_html($_POST[ 'meta-doctors-phone' ]) );
    }
    
    // Save facebookurl
    if( isset( $_POST[ 'meta-tfacebookurl' ] ) ) {
        update_post_meta( $post_id, 'meta-tfacebookurl', esc_url($_POST[ 'meta-tfacebookurl' ]) );
    }
    // Save linkdenurl
    if( isset( $_POST[ 'meta-tlinkdenurl' ] ) ) {
        update_post_meta( $post_id, 'meta-tlinkdenurl', esc_url($_POST[ 'meta-tlinkdenurl' ]) );
    }
    if( isset( $_POST[ 'meta-ttwitterurl' ] ) ) {
        update_post_meta( $post_id, 'meta-ttwitterurl', esc_url($_POST[ 'meta-ttwitterurl' ]) );
    }
    // Save googleplusurl
    if( isset( $_POST[ 'meta-tgoogleplusurl' ] ) ) {
        update_post_meta( $post_id, 'meta-tgoogleplusurl', esc_url($_POST[ 'meta-tgoogleplusurl' ]) );
    }

    // Save Instagram
    if( isset( $_POST[ 'meta-tinstagram' ] ) ) {
        update_post_meta( $post_id, 'meta-tinstagram', esc_url($_POST[ 'meta-tinstagram' ]) );
    }

    // Save Pinterest
    if( isset( $_POST[ 'meta-pinterest' ] ) ) {
        update_post_meta( $post_id, 'meta-pinterest', esc_url($_POST[ 'meta-pinterest' ]) );
    }
    // Save designation
    if( isset( $_POST[ 'meta-designation' ] ) ) {
        update_post_meta( $post_id, 'meta-designation', esc_html($_POST[ 'meta-designation' ]) );
    }
    // Save speciality
    if( isset( $_POST[ 'meta-doctor-speciality' ] ) ) {
        update_post_meta( $post_id, 'meta-doctor-speciality', esc_html($_POST[ 'meta-doctor-speciality' ]) );
    }
    // Save Percentage
    if( isset( $_POST[ 'meta-speciality-per' ] ) ) {
        update_post_meta( $post_id, 'meta-speciality-per', esc_html($_POST[ 'meta-speciality-per' ]) );
    }
}
add_action( 'save_post', 'vw_dentist_pro_posttype_ex_bn_metadesig_save' );

add_action( 'save_post', 'bn_meta_save' );
/* Saves the custom meta input */
function bn_meta_save( $post_id ) {
  if( isset( $_POST[ 'vw_dentist_pro_posttype_doctors_featured' ] )) {
      update_post_meta( $post_id, 'vw_dentist_pro_posttype_doctors_featured', esc_attr(1));
  }else{
    update_post_meta( $post_id, 'vw_dentist_pro_posttype_doctors_featured', esc_attr(0));
  }
}
/*------------------------------------- SHORTCODES -------------------------------------*/

/*------------------------------------- doctors Shorthcode -------------------------------------*/
function vw_dentist_pro_posttype_doctors_func( $atts ) {
  $doctors = '';
  $doctors = '<div class="row all-doctors">';
  $query = new WP_Query( array( 'post_type' => 'doctors') );

    if ( $query->have_posts() ) :

  $k=1;
  $new = new WP_Query('post_type=doctors');
  while ($new->have_posts()) : $new->the_post();
        $post_id = get_the_ID();
         $thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post_id), 'large' );
        if(has_post_thumbnail()) { $thumb_url = $thumb['0']; }
        $url = $thumb['0'];
        $custom_url ='';
        $doctors_desig= get_post_meta($post_id,'meta-designation',true);
        $doctors_phone= get_post_meta($post_id,'meta-doctors-phone',true);
        $doctors_email= get_post_meta($post_id,'meta-doctors-email',true);
        $facebookurl= get_post_meta($post_id,'meta-tfacebookurl',true);
        $linkedin=get_post_meta($post_id,'meta-tlinkdenurl',true);
        $twitter=get_post_meta($post_id,'meta-ttwitterurl',true);
        $instagram=get_post_meta($post_id,'meta-tinstagram',true);
        $googleplusurl= get_post_meta($post_id,'meta-tgoogleplusurl',true);
        $pinterest= get_post_meta($post_id,'meta-pinterest',true);
        $custom_url = get_permalink();
        $doctors .= '
            <div class="our_doctors_outer col-lg-6 col-md-6 col-sm-6">
              <div class="row doctors_inner">
                <div class="col-lg-6 col-md-12 our-doctors-content-box1">
                  <div class="our-doctors-content-box2">
                    <h4><a href="'.esc_url($custom_url).'">'.esc_html(get_the_title()) .'</a></h4>
                    <p>'.$doctors_desig.'</p>
                    <p>
                      <i class="fas fa-phone"></i>
                      '.$doctors_phone.'
                    </p>
                    <p>
                      <i class="far fa-envelope"></i>
                      '.$doctors_email.'
                    </p>
                    <div class="box-content">
                      <div class="att_socialbox">';
                        if($facebookurl != ''){
                          $doctors .= '<a class="" href="'.esc_url($facebookurl).'" target="_blank"><i class="fab fa-facebook-f"></i></a>';
                        } if($twitter != ''){
                          $doctors .= '<a class="" href="'.esc_url($twitter).'" target="_blank"><i class="fab fa-twitter"></i></a>';
                        } if($instagram != ''){
                          $doctors .= '<a class="" href="'.esc_url($instagram).'" target="_blank"><i class="fab fa-instagram align-middle" aria-hidden="true"></i></a>';
                        } if($linkedin != ''){
                          $doctors .= '<a class="" href="'.esc_url($linkedin).'" target="_blank"><i class="fab fa-linkedin-in"></i></a>';
                        }if($googleplusurl != ''){
                          $doctors .= '<a class="" href="'.esc_url($googleplusurl).'" target="_blank"><i class="fab fa-google-plus-g"></i></a>';
                        }if($pinterest != ''){
                          $doctors .= '<a class="" href="'.esc_url($pinterest).'" target="_blank"><i class="fab fa-pinterest-p align-middle " aria-hidden="true"></i></a>';
                        }
                      $doctors .= '</div>
                    </div>
                  </div>
                </div>
                <div class="col-lg-6 col-md-12 our-doctors-image">
                  <div class="box section-doctors-image">
                    <img class="classes-img" src="'.esc_url($thumb_url).'" alt="attorney-thumbnail" />
                  </div>
                </div>
              </div>
            </div>';
    if($k%2 == 0){
      $doctors.= '<div class="clearfix"></div>';
    }
      $k++;
  endwhile;
  else :
    $doctors = '<h2 class="center">'.esc_html__('Post Not Found','vw_dentist_pro_posttype').'</h2>';
  endif;
  return $doctors;
}

add_shortcode( 'vw-dentist-pro-doctors', 'vw_dentist_pro_posttype_doctors_func' );
