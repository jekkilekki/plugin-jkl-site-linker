<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/* Prevent direct access */
if ( ! defined( 'ABSPATH' ) ) exit;

class JKL_Site_Linker_Posttype {
    
    /**
     * Creates and registers the Custom Post Type
     */
    public function register_post_type() {
        $labels = array(
                'name'              => __( 'JKL Site Linker', 'jkl-site-linker' ),
                'singular_name'     => __( 'Page of Links', 'jkl-site-linker' ),
                'add_new'           => __( 'Add New', 'jkl-site-linker' ),
                'add_new_item'      => __( 'Add New Page of Links', 'jkl-site-linker' ),
                'edit'              => __( 'Edit', 'jkl-site-linker' ),
                'edit_item'         => __( 'Edit Page of Links', 'jkl-site-linker' ),
                'new_item'          => __( 'New Page of Links', 'jkl-site-linker' ),
                'view'              => __( 'View Page of Links', 'jkl-site-linker' ),
                'view_item'         => __( 'View Page of Links', 'jkl-site-linker' ),
                'search_items'      => __( 'Search Pages of Links', 'jkl-site-linker' ),
                'not_found'         => __( 'No Pages of Links found', 'jkl-site-linker' ),
                'not_found_in_trash'=> __( 'No Pages of Links found in Trash', 'jkl-site-linker' ),
        );
        
        $args = array(
                'labels'            => $labels,
                'public'            => true,
                'query_var'         => true,
                'capability_type'   => 'post',
                'has_archive'       => false,
                'hierarchical'      => false,
                'exclude_from_search'   => false,
                'menu_position'         => 30,
                'supports'              => array( 'title', 'author' ),
                'rewrite'               => array(
                            'slug'          => apply_filters( 'jklsl_prefix_slug', 'go' ),
                            'with_front'    => false,
                ),
        );
        
        register_post_type( 'jkl_site_linker',
                apply_filters( 'jkl_site_linker_register_posttype_args', $args )
        );
    } // END register_post_type()
    
    /**
     * Sets the Post message strings.
     * 
     * @global type $post
     * @param type $messages
     * @return string
     */
    public function post_updated_messages( $messages ) {
        global $post;
        
        $messages[ 'jkl-site-linker' ] = array(
            0 => '', // Unused. Messages start at index 1.
            1 => __( 'Page of Links updated.', 'jkl-site-linker' ),
            2 => __( 'Custom field updated.', 'jkl-site-linker' ),
            3 => __( 'Custom field deleted.', 'jkl-site-linker' ),
            4 => __( 'Page of Links updated.', 'jkl-site-linker' ),
            /* translators: %s: date and time of the revision */
            5 => isset( $_GET[ 'revision' ] ) ? sprintf( __( 'Page of Links restored to revision from %s.', 'jkl-site-linker' ), wp_post_revision_title( (int) $_GET[ 'revision' ], false ) ) : false,
            6 => __( 'Page of Links published.', 'jkl-site-linker' ),
            7 => __( 'Page of Links saved.', 'jkl-site-linker' ),
            8 => __( 'Page of Links submitted.', 'jkl-site-linker' ),
            9 => sprintf( __( 'Post scheduled for: <strong>%1$s</strong>.', 'jkl-site-linker' ), 
                    // translators: Publish box date format, see http://php.net/date
                    date_i18n( __( 'M j, Y @ G:i', 'jkl-site-linker' ), strtotime( $post->post_date ) ) ),
            10 => __( 'Page of Links draft updated.', 'jkl-site-linker' ),
        );
        
        return $messages;
    }
    
    /**
     * Adds additional link to GitHub repo to the Plugin Activation screen
     * 
     * @param type $links
     * @return type
     */
    public function plugin_action_links( $links ) {
        $settings_link = sprintf( '<a href="%s" target="_blank">%s</a>', 'https://github.com/jekkilekki/plugin-jkl-site-linker', __( 'GitHub', 'jkl-site-linker' ) );
        array_unshift( $links, $settings_link );
        
        return $links;
    }
    
    /**
     * Manipulates the visible columns on the All Pages of Links admin screen
     * 
     * @param type $columns
     */
    public function admin_cpt_columns( $columns ) {
        return array(
            'cb'                => '<input type="checkbox">',
            'title'             => __( 'Title', 'jkl-site-linker' ),
            // 'jklsl_url'        => __( 'Redirect to', 'jkl-site-linker' ),
            'jklsl_permalink'   => __( 'Permalink', 'jkl-site-linker' ),
            'jklsl_clicks'      => __( 'Clicks', 'jkl-site-linker' ),
            'author'            => __( 'Author', 'jkl-site-linker' ),
            'date'              => __( 'Date', 'jkl-site-linker' ),
        );
    }
    
    /**
     * Creates the custom columns on the admin screen
     * 
     * @global type $post
     * @param type $column
     */
    public function custom_columns( $column ) {
        global $post;
        
        switch( $column ) {
            case 'jklsl_url':
                echo make_clickable( get_post_meta( $post->ID, '_jklsl_redirect', true ) );
                break;
            case 'jklsl_permalink':
                echo '<input type="text" class="jkl-site-linker-permalink-copy-paste" value="' . esc_attr( get_permalink( $post->ID ) ) . '" readonly>';
                break;
            case 'jklsl_clicks':
                echo absint( get_post_meta( $post->ID, '_jklsl_count', true ) );
                break;
        }
    }
    
    /**
     * Registers the Link URL Meta box with WordPress
     */
    public function register_meta_box() {
        add_meta_box(
                'jklsl-url-infomation',                     // ID
                __( 'Sites to Link to', 'jkl-site-linker' ),       // Title
                array( &$this, 'render_meta_box' ),         // Callback
                'jkl_site_linker',                          // Post Type
                'normal',                                   // Context
                'high'                                      // Priority
        );
    }
    
    /**
     * Create the view for the Meta box on the Custom Post Type screen
     * 
     * @param type $post
     */
    public function render_meta_box( $post ) {
        wp_nonce_field( basename( __FILE__ ), '_jklsl_meta_box_nonce' );
        
        echo '<div class="jklsl-links-list-div">';
        echo '<ol id="jklsl-links-list" class="jklsl-list connectedSortable">';
        
        $links = get_post_meta( get_the_ID(), 'jklsl-links-list', true );
        var_dump( $_POST );
        foreach ( $links as $link ) :
            echo '<li class="sortable">';

            $field_id = $link;
            echo strtr( '<input type="url" id="{name}" name="{name}" value="{value}" placeholder="{placeholder}" class="large-text">', array(
                    '{name}'    => $field_id,
                    '{placeholder}' => __( 'http://link.com/', 'jkl-site-linker' ),
                    '{value}'       => esc_attr( get_post_meta( $post->ID, $field_id, true ) ),
            ) );

            // $counter = absint( get_post_meta( $post->ID, '_jklsl_count', true ) );
            // printf( '<span class="description">' . __( 'This Link has been accessed <strong>%d</strong> times.', 'jkl-site-linker' ) . '</span>', $counter );

//            $description = 'jklsl-link-description-1';
//            echo strtr( '<span><textarea id="{name}" name="{name}" placeholder="{placeholder}" class="large-text">{value}</textarea></span>', array(
//                    '{name}'    => $description,
//                    '{placeholder}' => __( 'Enter notes about the site here.', 'jkl-site-linker' ),
//                    '{value}'       => esc_attr( get_post_meta( $post->ID, $description, true ) ),
//            ) );

            echo '<input type="submit" name="jklsl-link-label-1-remove" id="jklsl-link-1-remove" class="jklsl-remove-item button hidden" value="x">';
            echo '</li>';
        endforeach;
        
        echo '</ol>';
        echo '<input type="submit" class="jklsl-add-item button" value="+">';
        echo '</div>';
        
    }
    
    /**
     * Verifies that the post type that's being saved is a JKL_Site_Linker post type.
     * @link    http://code.tutsplus.com/tutorials/creating-maintainable-wordpress-meta-boxes-verify-and-sanitize--cms-22488
     * 
     * @since   0.0.1
     * @access  private
     * @return  bool    Return true if the current post type is a 'jkl_site_linker' type; false otherwise.
     */
    private function is_valid_post_type() {
        return ! empty( $_POST[ 'post-type' ] ) && 'jkl_site_linker' == $_POST[ 'post_type' ];
    }
    
    /**
     * Determines whether or not the current user has the ability to save meta data for this post.
     * @link    http://code.tutsplus.com/tutorials/creating-maintainable-wordpress-meta-boxes-verify-and-sanitize--cms-22488
     * 
     * @since   0.0.1
     * 
     * @access  private
     * @param   int     $post_id        The ID of the post being saved
     * @param   string  $nonce_action   The name of the action associated with the none
     * @param   string  $nonce_id       The ID of the nonce field
     * @return  bool                    Whether or not the user has the ability to save this post
     */
    private function user_can_save( $post_id, $nonce_action, $nonce_id ) {
        
        $is_autosave = wp_is_post_autosave( $post_id );
        $is_revision = wp_is_post_revision( $post_id );
        $is_valid_nonce = ( isset ( $_POST[ $nonce_action ] ) && wp_verify_none( $_POST[ $nonce_action ], $nonce_id ) );
        
        // Return true if the user is able to save, false otherwise
        return ! ( $is_autosave || $is_revision ) && $is_valid_nonce;
    }
    
    /**
     * Sanitizes and serializes the information associated with this Post.
     * @link    http://code.tutsplus.com/tutorials/creating-maintainable-wordpress-meta-boxes-verify-and-sanitize--cms-22488
     * 
     * @since   0.0.1
     * 
     * @param   int     $post_id    The ID of the post that's currently being edited.
     * @return  void
     */
    public function save_post( $post_id ) {
        
        /*
         * If we're not working with a 'jkl_site_linker' post type or the user
         * doesn't have permission to save, then we exit the function.
         */
        if ( ! $this->is_valid_post_type() || ! $this->user_can_save( $post_id, '_jklsl_meta_box_nonce', basename( __FILE__ ) ) ) {
            return;
        }
        
//        if ( ! isset( $_POST[ '_jklsl_meta_box_nonce' ] ) || ! wp_verify_nonce( $_POST[ '_jklsl_meta_box_nonce' ], basename( __FILE__ ) ) )
//            return;
//        
//        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
//            return;
//        
//        if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) 
//            return;
//        
//        if ( defined( 'DOING_CRON' ) && DOING_CRON ) 
//            return;
        
//        if ( isset( $_POST[ '_jklsl_redirect' ] ) )
//            update_post_meta( $post_id, '_jklsl_redirect', $_POST[ '_jklsl_redirect' ] );
//        else 
//            delete_post_meta( $post_id, '_jklsl_redirect' );
        
        // If the Link inputs exist, iterate through them and sanitize them
        if ( ! empty( $_POST[ 'jklsl-links-list' ] ) ) {
            
            $links = $_POST[ 'jklsl-links-list' ];
            $sanitized_links = array();
            foreach ( $links as $link ) {
                
                $link = esc_url( strip_tags( $link ) );
                if ( ! empty( $link ) ) {
                    $sanitized_links[] = $link;
                }
                
            }
            
            update_post_meta( $post_id, 'jklsl-links-list', $sanitized_links );
            
        } else {
            
            if ( '' !== get_post_meta( $post_id, 'jklsl-links-list', true ) ) {
                delete_post_meta( $post_id, 'jklsl-links-list' );
            }
            
        }
        
    }
    
    public function count_and_redirect() {
        if ( ! is_singular( 'jkl_site_linker' ) )
            return;
        
        $counter = absint( get_post_meta( get_the_ID(), '_jklsl_count', true ) );
        update_post_meta( get_the_ID(), 'jklsl_count', ++$counter );
        
        $redirect_url = esc_url_raw( get_post_meta( get_the_ID(), '_jklsl_redirect', true ) );
        
        if ( ! empty( $redirect_url ) )
            wp_redirect( $redirect_url, 301 );
        else 
            wp_redirect( home_url(), 302 );
        
        die();
        
    }
    
    /**
     * Add Dashboard Widget for JKL Site Linker
     */
    public function jklsl_add_dashboard_widget() {
        wp_add_dashboard_widget(
                'jklsl_dashboard_widget',
                __( 'Pages with Links - Top 10', 'jkl-site-linker' ),
                array( &$this, 'jklsl_dashboard_widget_function' )
        );
    }
    
    /**
     * Add Dashboard Function for JKL Site Linker
     */
    public function jklsl_dashboard_widget_function() {
        $posts = get_posts(
                array(
                    'post_type'     => 'jkl_site_linker',
                    'post_status'   => 'publish',
                    'fields'        => 'ids',
                    'meta_key'      => '_jklsl_count',
                    'orderby'       => 'meta_value_num',
                    'order'         => 'DESC',
                    'posts_per_page'=> 10,
                )
        );
        
        if ( empty( $posts ) ) {
            echo '<p>' . __( 'There are no stats available yet!', 'jkl-site-linker' ) . '</p>';
            return;
        }
        
        // View
        ?>

        <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <thead>
                <tr align="<?php echo is_rtl() ? 'right' : 'left'; ?>">
                    <th scope="col"><?php _e( 'Redirect to', 'jkl-site-linker' ); ?></th>
                    <th scope="col"><?php _e( 'Edit', 'jkl-site-linker' ); ?></th>
                    <th scope="col"><?php _e( 'Clicks', 'jkl-site-linker' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                // loop over each post
                foreach ( $posts as $post_id ) :
                    // Get the meta needed
                    $link       = get_post_meta( $post_id, '_jklsl_redirect', true );
                    $link_count = absint( get_post_meta( $post_id, '_jklsl_count', true ) );
                    ?>
                <tr>
                    <td><a target="_blank" href="<?php echo $link; ?>"><?php echo $link; ?></a></td>
                    <td><a href="<?php echo get_edit_post_link( $post_id ); ?>"><?php _e( 'Edit', 'jkl-site-linker' ); ?></a></td>
                    <td><?php echo $link_count; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php
    }
    
    /**
     * Add order by Clicks
     * 
     * @param   array   $columns
     * @return  array
     */
    public function sortable_jklsl_clicks_column( $columns ) {
        $columns[ 'jklsl_clicks' ] = 'jklsl_clicks';
        
        return $columns;
    }
    
    /**
     * Add order by Clicks
     * 
     * @param   WP_Query    $query
     */
    public function clicks_orderby( $query ) {
        if ( ! is_admin() )
            return;
        
        $orderby = $query->get( 'orderby' );
        
        if ( 'jklsl_clicks' == $orderby ) {
            $query->set( 'meta_key', '_jklsl_count' );
            $query->set( 'orderby', 'meta_value_num' );
        }
    }
    
    /**
     * Add filter by Author
     */
    public function jklsl_filter_by_author() {
        global $typenow;
        
        if( 'jkl_site_linker' === $typenow ) {
            wp_dropdown_users(
                    array(
                        'name'              => 'author',
                        'show_option_all'   => __( 'View all authors', 'jkl-site-linker' ),
                    )
            );
        }
    }
    
    /**
     * Add external CSS Stylesheet file
     * 
     * @param   $hook
     */
    public function dashboard_widget_jklsl_external_css( $hook ) {
        global $typenow;
        
        $include_style = false;
        if ( 'index.php' === $hook )
            $include_style = true;
        
        if ( 'edit.php' === $hook && 'jkl_site_linker' === $typenow )
            $include_style = true;
        
        if ( ! $include_style )
            return;
        
        wp_enqueue_style( 'jklsl-dashboard-widget-styles', JKLSL_PLUGIN_URL . '/css/admin-style.css' );
    }
    
    public function jklsl_enqueue_scripts() {
        
        if( 'jkl_site_linker' === get_current_screen()->id ) {
            
            wp_enqueue_script( 'jquery-ui-sortable' );
            
            wp_enqueue_script(
                    'jklsl-add-links',
                    plugins_url( 'jkl-site-linker/js/add-links.js' ),
                    array( 'jquery' ),
                    '20160428'
            );
            
        }
        
    }
    
    public function __construct() {
        
        add_action( 'init', array( &$this, 'register_post_type' ) );
        add_filter( 'post_updated_messages', array( &$this, 'post_updated_messages' ) );
        
        add_action( 'admin_menu', array( &$this, 'register_meta_box' ) );
        add_filter( 'plugin_action_links_' . JKLSL_BASE, array( &$this, 'plugin_action_links' ) );
        
        add_filter( 'manage_edit-jklsl_columns', array( &$this, 'admin_posttype_columns' ) );
        add_action( 'manage_posts_custom_column', array( &$this, 'custom_columns' ) );
        add_action( 'save_post', array( &$this, 'save_post' ) );
        add_action( 'template_redirect', array( &$this, 'count_and_redirect' ) );
        
        // Add Dashboard Widget for JKL Site Linker
        add_action( 'wp_dashboard_setup', array( &$this, 'jklsl_add_dashboard_widget' ) );
        
        // Add order by Clicks
        add_action( 'pre_get_posts', array( &$this, 'clicks_orderby' ) );
        add_filter( 'manage_edit-jklsl_sortable_columns', array( &$this, 'sortable_jklsl_clicks_column' ) );
        
        // Add filter by Author
        add_action( 'restrict_manage_posts', array( &$this, 'jklsl_filter_by_author' ) );
        
        // Add external CSS stylesheet file
        add_action( 'admin_enqueue_scripts', array( &$this, 'dashboard_widget_jklsl_external_css' ) );
        add_action( 'admin_enqueue_scripts', array( &$this, 'jklsl_enqueue_scripts' ) );
        
    }
   
} // END class JKL_Site_Linker_Posttype

