<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/* Prevent direct access */
if ( ! defined( 'ABSPATH' ) ) exit; 

class JKL_Site_Linker_Maintenance {
    
    public static function activation() {
        JKL_Site_Linker_Main::instance()->posttype->register_post_type();
        flush_rewrite_rules();
    }
    
} // END class JKL_Site_Linker_Maintenance

register_activation_hook( JKLSL__FILE__, array( 'JKL_Site_Linker_Maintenance', 'activation' ) );
register_uninstall_hook( JKLSL__FILE__, 'flush_rewrite_rules' );