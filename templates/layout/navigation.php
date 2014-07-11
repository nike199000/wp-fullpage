<?php

/**
 * The template for displaying the Fullpage Navigation
 */
?>

<?php

	$position = WPFP_Query()->fullpage->fullpage_options['navigationPosition'];

?>

<div id="wpfp-navigation" class="<?php print $position; ?>">
	<ul>
		<?php 

			foreach( WPFP_Query()->sections as $key => $section ) :

			?>

				<li class="<?php print $key === 0 ? 'active' : ''; ?>" data-menuanchor="<?php print $section->post_name ?>" data-tooltip="<?php print esc_html( WPFP_Query()->get_section_nav_title( $key ) ); ?>">
					<a href="#<?php print $section->post_name ?>">
						<span></span>
					</a>
				</li>

			<?php 

			endforeach;

		?>
	</ul>
</div>