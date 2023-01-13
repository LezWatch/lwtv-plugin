<?php
/**
 * The template for displaying the mini stats section on Actor Pages
 *
 * I don't like how it formats with the height...
 *
 * @package LezWatch.TV
 */

?>
<div class="container chart-container">
	<div class="row">
		<div class="col-6">
			<h5>Roles</h5>
			<?php ( new LWTV_Stats() )->generate( 'actors', 'actor_char_roles', 'piechart', get_the_id() ); ?>
		</div>
		<div class="col-6">
			<h5>Status</h5>
			<?php ( new LWTV_Stats() )->generate( 'actors', 'actor_char_dead', 'piechart', get_the_id() ); ?>
		</div>
	</div>
</div>
