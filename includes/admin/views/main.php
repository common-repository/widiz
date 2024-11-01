<?php
/**
 * Represents the view for the administration dashboard.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
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

<div class="wrap">
<pre>
<?php print_r(wpcf7_contact_form(128)->scan_form_tags()); ?>
</pre>
    <h2><?php echo esc_html(get_admin_page_title()); ?></h2>

    <!-- @TODO: Provide markup for your options page here. -->

</div>
