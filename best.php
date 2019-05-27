<?php
/**
* Plugin Name: Best hosting
 * Description: The plugin create table of own content like rating view.
 * Version:  0.1
 * Network: true
 */
use Carbon_Fields\Container;
use Carbon_Fields\Field;

// init new post type "Rating"
function rating() {
    $labels = array(
        'name'               => 'Hosting rating',
        'singular_name'      => 'Rating',
        'menu_name'          => 'Hosting ratings',
        'name_admin_bar'     => 'Rating',
        'add_new'            => 'Add New',
        'add_new_item'       => 'Add New Rating',
        'new_item'           => 'New Rating',
        'edit_item'          => 'Edit Rating',
        'view_item'          => 'View Rating',
        'all_items'          => 'All Ratings',
        'search_items'       => 'Search Ratings',
        'parent_item_colon'  => 'Parent Rating',
        'not_found'          => 'No Ratings Found',
        'not_found_in_trash' => 'No Ratings Found in Trash'
    );

    $args = array(
        'labels'              => $labels,
        'public'              => true,
        'exclude_from_search' => false,
        'publicly_queryable'  => true,
        'show_ui'             => true,
        'show_in_nav_menus'   => true,
        'show_in_menu'        => true,
        'show_in_admin_bar'   => true,
        'menu_position'       => 5,
        'menu_icon'           => 'dashicons-admin-appearance',
        'capability_type'     => 'post',
        'hierarchical'        => false,
        'supports'            => array( 'title', 'editor', 'author', 'thumbnail', 'comments' ),
        'has_archive'         => true,
        'rewrite'             => array( 'slug' => 'rating' ),
        'query_var'           => true
    );

    register_post_type( 'sm_rating', $args );
}
add_action( 'init', 'rating' );

$available_on = array(
	'fab fa-windows' => 'Windows',
	'fab fa-app-store-ios' => 'Mac OS/ OSX',
	'fab fa-linux' => 'Linux',
	'fab fa-android' => 'Android',
	'fas fa-mobile-alt' => 'Mobile',
	'fas fa-tv' => 'Smart TV',
	'fas fa-desktop' => 'Desktop',
	'fas fa-broadcast-tower' => 'Router',
	'fas fa-microchip' => 'Raspberry Pi',
) ;

/**
 * init needed meta-fields for the new post type
 *  Used GLOBAL var $available_on, because  no another way to pass argument to WP hook.
 */
add_action( 'carbon_fields_register_fields', function() { global $available_on; crb_attach_post_meta($available_on); });
function crb_attach_post_meta($available_on) {

	Container::make( 'post_meta', __( 'Rating items' ) )
		//->show_on_post_type('sm_rating')
		->where( 'post_type', '=', 'sm_rating' )
	         ->add_fields( array(
		         Field::make( 'complex', 'rating_items', __( 'Items' ) )
			         //->set_layout( 'tabbed-vertical' )
		              ->setup_labels( array(
			              'plural_name' => __( 'Items' ),
			              'singular_name' => __( 'Item' ),
		              ) )
		              ->add_fields( array(
			              Field::make( 'image', 'hosting_logo', 'Hosting logo' )
				              ->set_width(20)
			                   ->set_value_type( 'url' ),
			              Field::make( 'textarea', 'hosting_description', 'Short description' )
			                   ->set_width( 80 )
			                   ->set_rows(6),
			              Field::make('text', 'stars_rating', 'Stars rating')
				              ->set_width( 50 )
				              ->set_attribute('min', '1')
				              ->set_attribute('step', '.5')
				              ->set_attribute('type', 'number')
				              ->set_attribute('maxLength', '1')
				              ->set_attribute('max', '5'),
			              Field::make( 'text', 'hosting_url', 'Hosting URL' )
				              ->set_width( 50 )
				              ->set_required( true )
			                   ->set_attribute( 'type', 'url' ),
			              Field::make( 'text', 'hosting_score', 'Score' )
			                   ->set_width( 50 )
			                   ->set_attribute('min', '0')
			                   ->set_attribute('step', '.1')
			                   ->set_attribute('type', 'number')
			                   ->set_attribute('maxLength', '3')
			                   ->set_attribute('max', '10'),
			              Field::make( 'text', 'hosting_badge', 'Badge' )
				              ->set_width( 50 )
			                   ->set_attribute( 'placeholder', 'Example: Best VPN' )
				              ->set_attribute('maxLength', '20'),
			              Field::make( "multiselect", "available_on", "Available on" )
				              ->set_width( 50 )
			                   ->add_options($available_on),
			              Field::make( 'complex', 'features' )
			                   ->add_fields( array(
				                   Field::make( 'text', 'hosting_features' )
			                   ->set_attribute('placeholder', 'Example: Live Chat customer support'),
			                   )),
		              ) ),
	         ) );
}

function hosting_stars ($num) {
	$int_val = intval($num);
	$res = '';
	$check = $num - $int_val;
	$stars_count = 0;
	for ($i=0; $i < $int_val; $i++, $stars_count++) {
		$res .= '<span class="full-star"><i class="fas fa-star"></i></span>';
	}
	if ($check > 0) {
		$stars_count++;
		$res .= '<span class="half-star"><i class="fas fa-star-half"></i></span>';
	}
	if ($stars_count <= 5) {
		for ($stars_count; $stars_count < 5; $stars_count++) {
			$res .= '<span class="empty-star"><i class="far fa-star"></i></span>';
		}
	}

	return $res;
}

add_action( 'after_setup_theme', 'crb_load' );
function crb_load() {
	require_once( 'vendor/autoload.php' );
	\Carbon_Fields\Carbon_Fields::boot();
}

add_filter('single_template', 'my_custom_template');
function my_custom_template($single) {
	global $wp_query, $post;
	/* Checks for single template by post type */
	if ( $post->post_type == 'sm_rating' ) {
		if ( file_exists( plugin_dir_path(__FILE__) . '/content-sm_rating.php' ) ) {
			return plugin_dir_path(__FILE__) . '/content-sm_rating.php';
		}
	}
	return $single;
}

add_action('wp_enqueue_scripts', 'callback_for_setting_up_scripts');
function callback_for_setting_up_scripts() {
	wp_register_style( 'namespace',  plugins_url('rating.css',__FILE__ ));
	wp_enqueue_style( 'namespace' );
	wp_enqueue_script( 'namespaceformyscript',  plugins_url('fontawesome-all.min.js',__FILE__ ));
	wp_enqueue_script( 'rating',  plugins_url('rating.js',__FILE__ ), array ( 'jquery' ), 1, true);
}

// Add Shortcode
function rating_shortcode( $atts ) {
	// Attributes
	$atts = shortcode_atts(
		array(
			'id' => '0',
		),
		$atts,
		'rating'
	);
	return get_rating($atts['id']);
}
add_shortcode( 'rating', 'rating_shortcode' );

function get_rating($id) {
	global $available_on;
	$res = '';
	$ratings = carbon_get_post_meta($id, 'rating_items');
	//var_dump($ratings);
	$post = get_post($id);
	$res .= "<div class='content'>".$post->post_content."</div><div class='rating-wrapper'>";
	$res .= "<div class='rating-header'>
	    <div class='rating-provider-title'>VPN Provider</div>
	    <div class='rating-feature-title'>Features</div>
	    <div class='rating-score-title'>Our Score</div>
	</div>";
	foreach ($ratings as $rating) {
$res .= "<div class='rating-container'>
			<div class='hosting-logo'>
				<img src='{$rating['hosting_logo']}'>";
		if ($rating['hosting_badge']) {
			$res .= "<div class='badge'>{$rating['hosting_badge']}</div>";
		}
			if ($rating['stars_rating']):
        $res .=  "<div class='hosting-stars'>".hosting_stars($rating['stars_rating'])."</div>";
		endif;
		$res .= "</div>
			<div class='main-info'>
				<div class='short-description'>{$rating['hosting_description']}</div>";
		if ($rating['features']) {
			$res .= "<div class='rating-features'>";
			foreach ( $rating['features'] as $feature ) {
				$res .= "<div class='rating-feature'>
					<i class='far fa-check-circle'></i>
				{$feature['hosting_features']}</div>";
			}
			$res .= "</div>";
		}
		if ($rating['available_on']) {
			$res .= "<div class='rating-availableon'>";
			foreach ( $rating['available_on'] as $available ) {
				$res .= "<div class='tooltip'><i class='fab {$available} fa-2x'></i>
                    <span class='tooltiptext'>{$available_on[$available]}</span>
                </div>";
			}
			$res .= "</div>";
		}
		$res.="</div>
		  <div class='rating-score-wrap'>
	<div class='rating-score'>{$rating['hosting_score']}</div>
	</div>
<div class='visit-link'><a href='{$rating['hosting_url']}'>Visit site</a></div>
</div>";
	}
	$res.="</div>";
	return $res;
}

//add Shortcode column to rating list in admin part
add_filter('manage_edit-sm_rating_columns', 'my_columns');
function my_columns($columns) {
	$columns['shortcode'] = 'Shortcode';
	return $columns;
}
add_action( 'manage_sm_rating_posts_custom_column' , 'custom_sm_rating_column', 10, 2 );
function custom_sm_rating_column( $column, $post_id ) {
	switch ( $column ) {
		case 'shortcode' :
			echo "<pre>[rating id='$post_id']</pre>";
			break;
	}
}