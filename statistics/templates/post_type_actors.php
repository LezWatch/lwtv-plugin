<?php
/**
 * The template for displaying the mini stats section on Actor Pages
 *
 * @package LezWatch.TV
 */

?>
<div class="container chart-container">
	<div class="row">
		<div class="col-6">
			<?php ( new LWTV_Stats() )->generate( 'actors', 'actor_char_roles', 'piechart', get_the_id() ); ?>
		</div>
		<div class="col-6">
			<?php ( new LWTV_Stats() )->generate( 'actors', 'actor_char_dead', 'piechart', get_the_id() ); ?>
		</div>
	</div>
</div>