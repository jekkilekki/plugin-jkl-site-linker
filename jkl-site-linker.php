<?php
/**
 * @since       0.0.1
 * @package     JKL_Site_Linker
 * @author      Aaron Snowberger <jekkilekki@gmail.com>
 * 
 * @wordpress-plugin
 * Plugin Name: JKL Site Linker
 * Plugin URI:  http://github.com/jekkilekki/plugin-jkl-site-linker
 * Description: Adds a Custom Site Links Post Type that allows you to add a list of external links that will automagically be posted as thumbnails and Site Titles on your Page.
 * Author:      Aaron Snowberger
 * Author URI:  http://aaronsnowberger.com
 * Version:     0.0.1
 * Text Domain: jkl-site-linker
 * Domain Path: /languages/
 * License:     GPL2
 * 
 * Requires at least: 3.5
 * Tested up to: 4.5
 */

/**
 * JKL Site Linker allows you to add a list of URLs to a Page and have them display as thumbnail links.
 * Copyright (C) 2016  AARON SNOWBERGER (email: JEKKILEKKI@GMAIL.COM)
 * 
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

/**
 * Plugin Notes:
 * 1. Separate out Model, View, Controller stuff - subdivide out classes, specific functionality, etc
 * 2. Options page/settings
 *      1. Default = Display links as thumbnails + site title + description + user notes + ???
 *      2. Option2 = Display links as <ol> or <ul> with or without thumbnails
 * 3. Change plugin name to 'JKL_Links_Collector' or 'JKL_Links_Collections'?
 */

/* Prevent direct access */
if ( ! defined( 'ABSPATH' ) ) exit;

define( 'JKLSL__FILE__', __FILE__ );
define( 'JKLSL_BASE', plugin_basename( JKLSL__FILE__ ) );
define( 'JKLSL_PLUGIN_URL', plugins_url( '', JKLSL__FILE__ ) );

/*
 * The class that represents the MAIN JKL ADMIN settings page
 */

/*
 * The class that represents and defines the core plugin
 */
require_once plugin_dir_path( __FILE__ ) . 'inc/class-jkl-site-linker-maintenance.php';
//include( 'inc/class-jkl-site-linker-maintenance.php' );

/*
 * The class that creates the Custom Post Type
 */
require_once plugin_dir_path( __FILE__ ) . 'inc/class-jkl-site-linker-posttype.php';
//include( 'inc/class-jkl-site-linker-posttype.php' );

final class JKL_Site_Linker_Main {
    
    /**
     * The instance of the plugin
     * 
     * @since   0.0.1
     * @access  private
     * @var     JKL_Site_Linker_Main    The one true JKL_Site_Linker_Main instance
     */
    private static $_instance = null;
    
    /**
     * The ID of the plugin.
     * 
     * @since   0.0.1
     * @access  private
     * @var     string  $name       The ID of the plugin.
     */
    private $name;

    /**
     * Current version of the plugin.
     * 
     * @since   0.0.1
     * @access  private
     * @var     string  $version    The current version of the plugin.
     */
    private $version;
    
    /**
     * Custom Post Type
     * 
     * @since   0.0.1
     * @access  public
     * @var     JKL_Site_Linker_Posttype    $posttype   The Custom Post Type for JKL_Site_Linker
     */
    public $posttype;
    
    /**
     * CONSTRUCTOR !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
     * Initializes the JKL_Site_Linker_Main object and sets its properties
     * 
     * @since   0.0.1
     * @var     string  $name       The name of the plugin.
     * @var     string  $version    The version of the plugin.
     */
    private function __construct( $name, $version ) {
        
        // Set the name and version number
        $this->name     = $name;
        $this->version  = $version;
        
        // Create the Custom Post Type
        $this->make_posttype();
        //$this->posttype = new JKL_Site_Linker_Posttype();
        
        // Load the plugin and supplementary files
        $this->load();
        
    }
    
    /**
     * Creates the Custom Post Type
     * 
     * @since   0.0.1
     * @return  object  $posttype   The Custom Post Type
     */
    protected function make_posttype() {
        
        if ( is_null( $this->posttype ) ) {
            $this->posttype = new JKL_Site_Linker_Posttype();
        }
        return $this->posttype;
        
    }
    
    /**
     * Loads translation directory
     * Adds the call to enqueue styles and scripts
     * 
     * @since   0.0.1
     */
    protected function load() {
        
        add_action( 'plugins_loaded', array( &$this, 'load_textdomain' ) );
        
    }
    
    public function load_textdomain() {
        load_plugin_textdomain( 'jkl-site-linker', false, basename( dirname( __FILE__ ) ) . '/languages/' );
    }
    
    /**
     * Throw error on object clone
     * 
     * The whole idea of the singleton design pattern is that there is a single object.
     * Therefore, we don't want the object to be cloned.
     * 
     * @since   0.0.1
     * @return  void
     */
    public function __clone() {
        // Cloning instances of the class is forbidden
        _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'jkl-site-linker' ), '0.0.1' );
    }
    
    /**
     * Disable unserializing of the class
     * 
     * @since   0.0.1
     * @return  void
     */
    public function __wakeup() {
        // Unserializing instances of the class is forbidden
        _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'jkl-site-linker' ), '0.0.1' );
    }
    
    /**
     * INSTANCE !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
     * Creates the Instance of the JKL_Site_Linker_Main object
     * 
     * @since   0.0.1
     * @return  JKL_Site_Linker_Main
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new JKL_Site_Linker_Main( 'jkl-site-linker', '0.0.1' );
        }
        return self::$_instance;
    }
    
    
} // END final class JKL_Site_Linker_Main

//if ( ! defined( 'JKL_SITE_LINKER_TESTS' ) ) {
    JKL_Site_Linker_Main::instance();
//}
