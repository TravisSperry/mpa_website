<?php
/**
 * Integrates this theme with the Our Team plugin
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Styles
 */
function woo_our_team_scripts() {
	wp_register_style( 'woo-our-team-css', get_template_directory_uri() . '/includes/integrations/our-team/css/our-team.css' );
	wp_enqueue_style( 'woo-our-team-css' );
}
add_action( 'wp_enqueue_scripts', 'woo_our_team_scripts' );

/**
 * Our Team template
 * @param  string $tpl Our Team Template
 */
function woo_customize_our_team_template ( $tpl ) {
	return '<div itemscope itemtype="http://schema.org/Person" class="%%CLASS%%">%%AVATAR%% <div class="wrap">%%TITLE%% <div id="team-member-%%ID%%"  class="team-member-text" itemprop="description">%%TEXT%% %%AUTHOR%%</div></div></div>';
}
add_filter( 'woothemes_our_team_item_template', 'woo_customize_our_team_template' );