<?php
/**
 * Widiz.
 *
 * @package   Widiz_Settings
 * @author    Widiz
 * @license   GPL-2.0+
 * @link      https://widiz.com
 * @copyright 2017 Widiz
 */

/**
 *-----------------------------------------
 * Do not delete this line
 * Added for security reasons: http://codex.wordpress.org/Theme_Development#Template_Files
 *-----------------------------------------
 */
defined('ABSPATH') or die("Direct access to the script does not allowed");
/*-----------------------------------------*/

/**
 * Plugin API
 */
class Widiz_Options
{
    static $options = [];

    static function create( $option, $value, $autoload = 'no' ) {

        self::$options[] = $option;

        return add_option( $option, $value, '', $autoload );

    }


    static function update( $option, $new_value, $autoload = 'no' ) {

        if ( ! in_array( $option, self::$options )) {
            self::$options[] = $option;
        }

        return update_option( $option, $new_value, $autoload );

    }


    static function read( $option, $default = false ) {

        return get_option( $option, $default );

    }


    static function delete( $option ) {

        return delete_option( $option );

    }
}
