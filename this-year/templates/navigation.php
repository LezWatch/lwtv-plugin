<?php
/**
 * The template for displaying the yearly navigation
 *
 * @package LezWatch.TV
 */

$thisyear = ( isset( $thisyear ) ) ? $thisyear : date( 'Y' );
$lastyear = FIRST_LWTV_YEAR;
$baseurl  = '/this-year/';
?>

<nav aria-label="This Year navigation" role="navigation">
	<ul class="pagination justify-content-center">

		<?php
		// If it's not 1961, we can show the first year we have queers
		if ( $thisyear !== $lastyear ) {
			?>
			<li class="page-item first mr-auto"><a href="<?php echo esc_url( $baseurl . $lastyear . '/' ); ?>" class="page-link"><?php echo lwtv_yikes_symbolicons( 'caret-left-circle.svg', 'fa-chevron-circle-left' ); ?> First (<?php echo (int) $lastyear; ?>)</a></li>
			<li class="page-item previous"><a href="<?php echo esc_url( $baseurl . ( $thisyear - 1 ) . '/' ); ?>" title="previous year" class="page-link"><?php echo lwtv_yikes_symbolicons( 'caret-left.svg', 'fa-chevron-left' ); ?> Previous</a></li>
			<li class="page-item"><a href="<?php echo esc_url( $baseurl . ( $thisyear - 2 ) . '/' ); ?>" class="page-link"><?php echo (int) ( $thisyear - 2 ); ?></a></li>
			<li class="page-item"><a href="<?php echo esc_url( $baseurl . ( $thisyear - 1 ) . '/' ); ?>" class="page-link"><?php echo (int) ( $thisyear - 1 ); ?></a></li>
			<?php
		}
		?>

		<li class="page-item active"><span class="active page-link"><?php echo (int) $thisyear; ?></span></li>

		<?php
		if ( date( 'Y' ) !== $thisyear ) {
			?>
			<li class="page-item"><a href="<?php echo esc_url( $baseurl . ( $thisyear + 1 ) . '/' ); ?>" class="page-link"><?php echo (int) ( $thisyear + 1 ); ?></a></li>
			<li class="page-item next"><a href="<?php echo esc_url( $baseurl . ( $thisyear + 1 ) . '/' ); ?>" class="page-link" title="next year">Next <?php echo lwtv_yikes_symbolicons( 'caret-right-circle.svg', 'fa-chevron-circle-right' ); ?></a></li>
			<li class="page-item last ml-auto"><a href="<?php echo esc_url( $baseurl . date( 'Y' ) . '/' ); ?>" class="page-link">Last (<?php echo (int) date( 'Y' ); ?>)<?php echo lwtv_yikes_symbolicons( 'caret-right.svg', 'fa-chevron-right' ); ?></a></li>
			<?php
		}
		?>
	</ul>
</nav><!-- .navigation -->
