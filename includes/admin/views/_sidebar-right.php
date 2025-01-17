<?php
/**
 * Right sidebar for settings page
 *
 * @package   Widiz_Admin
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
?>


<div id="postbox-container-1" class="postbox-container sidebar-right">
    <div class="meta-box-sortables">
        <div class="postbox">
            <h3><span><?php esc_attr_e('Get help', 'widiz');?></span></h3>
            <div class="inside">
                <div>
                    <ul>
                        <li><a class="no-underline" target="_blank" href="https://widiz.com"><span class="dashicons dashicons-admin-home"></span> <?php esc_attr_e('Plugin Homepage', 'widiz');?></a></li>
                    </ul>
                </div>
                <div class="sidebar-footer">
                    &copy; <?php echo date('Y'); ?> <a class="no-underline text-highlighted" href="https://widiz.com" title="Widiz" target="_blank">Widiz</a>
                </div>
            </div>
        </div>
    </div>
</div>
