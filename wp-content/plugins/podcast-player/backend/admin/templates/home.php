<?php
/**
 * Podcast player options home page
 *
 * @package Podcast Player
 * @since 3.3.0
 */

?>

	<div class="pp-welcome-wrapper">
		<div class="pp-welcome-main">
			<div class="pp-welcome-section">
				<h3>Getting Started with Podcast Player</h3>
				<div class="pp-welcome-video" style="max-width: 100%; overflow: hidden;">
				<?php
				if ( function_exists( 'wp_oembed_get' ) ) :
					echo wp_oembed_get(
						esc_url('https://www.youtube.com/watch?list=PLc4vyDJIvG8ehh-P7c2j_ZwIN_oVzXGXi&v=R_TpPo5f1fM'),
						array('width' => 760, 'height' => 428)
					);
				endif;
				?>
				</div>
			</div>
			<div class="pp-welcome-section">
				<h3>Important Links</h3>
				<ul class="pp-welcome-list">
					<li>To learn more about podcast player read <?php $this->mlink( 'https://vedathemes.com/docs7/', 'Documentation' ); ?></li>
					<li><?php $this->mlink( 'https://wordpress.org/support/plugin/podcast-player/', 'Ask a Question' ); ?> for Podcast Player free version.</li>
					<li><?php $this->mlink( 'https://vedathemes.com/contact-us-2/', 'Ask a Question' ); ?> for Podcast Player Pro.</li>
				</ul>
			</div>
		</div>
	</div>
