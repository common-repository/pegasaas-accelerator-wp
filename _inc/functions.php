<?php

function pa_web_perf($args) {
	pegasaas_accelerator_settings($args);
}

function pegasaas_accelerator_settings($args) { 
	global $pegasaas;
	
	if (isset($_GET['phpinfo']) && $_GET['phpinfo'] == 1) {
		ob_clean();
		phpinfo();
		PegasaasUtils::log("End of PHP Info", "script_execution_benchmarks");

		exit;
	} 
	
	$pegasaas->pre_condition_admin_page();
	
	if (isset($_GET['test'])) {
		include "admin-test-suite.php";
	} else {
		include "admin-control-panel.php";
	}
}


function debug_out($output) {
	if ($_GET['pegasaas_debug'] != "") {
		print "<pre class='admin'>";
		var_dump($output);
		print "</pre>";
	}
}
function pegasaas_get_home_path() {
 $home    = set_url_scheme( get_option( 'home' ), 'http' );
		
        $siteurl = set_url_scheme( get_option( 'siteurl' ), 'http' );
		

        if ( ! empty( $home ) && 0 !== strcasecmp( $home, $siteurl ) ) {
			
				$home_path_info = parse_url($home);
				$site_path_info = parse_url($siteurl);
		
				$wp_path_rel_to_home = str_ireplace(@$home_path_info['path'], '', @$site_path_info['path']);
		
				$pos = strripos( str_replace( '\\', '/', $_SERVER['SCRIPT_FILENAME'] ), trailingslashit( $wp_path_rel_to_home ) );
				
				// if we are not in the folder that contains the wordpress files (wp-admin, wp-includes)
				// then we should assume the current folder is the home folder
				if (!$pos) {
					$pos = strripos( str_replace( '\\', '/', $_SERVER['SCRIPT_FILENAME'] ), "/" );
				} 
			
				$home_path = substr( $_SERVER['SCRIPT_FILENAME'], 0, $pos );
				$home_path = str_replace("/wp-admin", "/", $home_path);
				$home_path = trailingslashit( $home_path );
	        } else {
			
		
	              $home_path = ABSPATH;
			}
		
		
	        return str_replace( '\\', '/', $home_path );	
			
	}

function pegasaas_get_content_folder() {
	$content_url_data = parse_url( content_url() );
	$home_url_data = parse_url( home_url() );
	
	
	$content_folder = str_replace(@$home_url_data['path'], "", @$content_url_data['path']);
	
	return $content_folder;

	
}

if (!function_exists('getallheaders')) { 
    function getallheaders() 
    { 
        $headers = []; 
       foreach ($_SERVER as $name => $value) 
       { 
           if (substr($name, 0, 5) == 'HTTP_') 
           { 
               $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value; 
           } 
       } 
       return $headers; 
    } 
} 

// because Divi does not provide the et_core_clear_wp_cache as a hook, 
// we have to make a copy of their function  (with our clear cache command within it)
// and make sure it is up-to-date on a regular basis
// Hey Divi guys!  Add our code to your function please! :)
if ( ! function_exists( 'et_core_clear_wp_cache' ) ):
function et_core_clear_wp_cache( $post_id = '' ) {
	if ( ! wp_doing_cron() && ! et_core_security_check_passed( 'edit_posts' ) ) {
		return;
	}

	try {
		// Pegasaas Accelerator WP
		if (class_exists('PegasaasCache')) {
			PegasaasCache::clear_et_cache( $post_id );
		}

		// Cache Plugins
		// Comet Cache
		if ( is_callable( 'comet_cache::clear' ) ) {
			comet_cache::clear();
		}

		// WP Rocket
		if ( function_exists( 'rocket_clean_post' ) ) {
			if ( '' !== $post_id ) {
				rocket_clean_post( $post_id );
			} else if ( function_exists( 'rocket_clean_domain' ) ) {
				rocket_clean_domain();
			}
		}

		// W3 Total Cache
		if ( has_action( 'w3tc_flush_post' ) ) {
			'' !== $post_id ? do_action( 'w3tc_flush_post', $post_id ) : do_action( 'w3tc_flush_posts' );
		}

		// WP Super Cache
		if ( function_exists( 'wp_cache_debug' ) && defined( 'WPCACHEHOME' ) ) {
			include_once WPCACHEHOME . 'wp-cache-phase1.php';
			include_once WPCACHEHOME . 'wp-cache-phase2.php';

			if ( '' !== $post_id && function_exists( 'clear_post_supercache' ) ) {
				clear_post_supercache( $post_id );
			} else if ( '' === $post_id && function_exists( 'wp_cache_clear_cache_on_menu' ) ) {
				wp_cache_clear_cache_on_menu();
			}
		}

		// WP Fastest Cache
		if ( isset( $GLOBALS['wp_fastest_cache'] ) ) {
			if ( '' !== $post_id && method_exists( $GLOBALS['wp_fastest_cache'], 'singleDeleteCache' ) ) {
				$GLOBALS['wp_fastest_cache']->singleDeleteCache( $post_id );
			} else if ( '' === $post_id && method_exists( $GLOBALS['wp_fastest_cache'], 'deleteCache' ) ) {
				$GLOBALS['wp_fastest_cache']->deleteCache();
			}
		}

		// WordPress Cache Enabler
		if ( has_action( 'ce_clear_cache' ) ) {
			'' !== $post_id ? do_action( 'ce_clear_post_cache', $post_id ) : do_action( 'ce_clear_cache' );
		}

		// LiteSpeed Cache
		if ( is_callable( 'LiteSpeed_Cache::get_instance' ) ) {
			$litespeed = LiteSpeed_Cache::get_instance();

			if ( '' !== $post_id && method_exists( $litespeed, 'purge_post' ) ) {
				$litespeed->purge_post( $post_id );
			} else if ( '' === $post_id && method_exists( $litespeed, 'purge_all' ) ) {
				$litespeed->purge_all();
			}
		}

		// LiteSpeed Cache v1.1.3+
		if ( '' !== $post_id && function_exists( 'litespeed_purge_single_post' ) ) {
			litespeed_purge_single_post( $post_id );
		} else if ( '' === $post_id && is_callable( 'LiteSpeed_Cache_API::purge_all' ) ) {
			LiteSpeed_Cache_API::purge_all();
		}

		// Hyper Cache
		if ( class_exists( 'HyperCache' ) && isset( HyperCache::$instance ) ) {
			if ( '' !== $post_id && method_exists( HyperCache::$instance, 'clean_post' ) ) {
				HyperCache::$instance->clean_post( $post_id );
			} else if ( '' === $post_id && method_exists( HyperCache::$instance, 'clean' ) ) {
				HyperCache::$instance->clean_post( $post_id );
			}
		}

		// Hosting Provider Caching
		// Pantheon Advanced Page Cache
		$pantheon_clear     = 'pantheon_wp_clear_edge_keys';
		$pantheon_clear_all = 'pantheon_wp_clear_edge_all';
		if ( function_exists( $pantheon_clear ) || function_exists( $pantheon_clear_all ) ) {
			if ( '' !== $post_id && function_exists( $pantheon_clear ) ) {
				pantheon_wp_clear_edge_keys( array( "post-{$post_id}" ) );
			} else if ( '' === $post_id && function_exists( $pantheon_clear_all ) ) {
				pantheon_wp_clear_edge_all();
			}
		}

		// Siteground
		if ( isset( $GLOBALS['sg_cachepress_supercacher'] ) ) {
			global $sg_cachepress_supercacher;

			if ( is_object( $sg_cachepress_supercacher ) && method_exists( $sg_cachepress_supercacher, 'purge_cache' ) ) {
				$sg_cachepress_supercacher->purge_cache( true );
			}

		} else if ( function_exists( 'sg_cachepress_purge_cache' ) ) {
			sg_cachepress_purge_cache();
		}

		// WP Engine
		if ( class_exists( 'WpeCommon' ) ) {
			is_callable( 'WpeCommon::purge_memcached' ) ? WpeCommon::purge_memcached() : '';
			is_callable( 'WpeCommon::clear_maxcdn_cache' ) ? WpeCommon::clear_maxcdn_cache() : '';
			is_callable( 'WpeCommon::purge_varnish_cache' ) ? WpeCommon::purge_varnish_cache() : '';

			if ( is_callable( 'WpeCommon::instance' ) && $instance = WpeCommon::instance() ) {
				method_exists( $instance, 'purge_object_cache' ) ? $instance->purge_object_cache() : '';
			}
		}

		// Bluehost
		if ( class_exists( 'Endurance_Page_Cache' ) ) {
			wp_doing_ajax() ? ET_Core_LIB_BluehostCache::get_instance()->clear( $post_id ) : do_action( 'epc_purge' );
		}

		// Complimentary Performance Plugins
		// Autoptimize
		if ( is_callable( 'autoptimizeCache::clearall' ) ) {
			autoptimizeCache::clearall();
		}

	} catch( Exception $err ) {
		ET_Core_Logger::error( 'An exception occurred while attempting to clear site cache.' );
	}
}
endif;

?>