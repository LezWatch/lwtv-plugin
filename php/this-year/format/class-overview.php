<?php
namespace LWTV\This_Year\Format;

use LWTV\This_Year\Generator;

class Overview {

	/**
	 * Output Default Data
	 *
	 * @param string $this_year   Year for Data
	 * @param array  $build_array Array of data to process
	 *
	 * @return N/A -- output
	 */
	public function make( $this_year, $build_array ) {
		$this_year = ( isset( $this_year ) ) ? $this_year : gmdate( 'Y' );
		$array     = $build_array;
		?>
		<div class="container">
			<div class="row">
				<div class="col">
					<div class="card text-center">
						<h3 class="card-header characters">Characters On Air</h3>
						<div class="card-body bg-light">
							<h5 class="card-title"><?php echo (int) $array['characters']; ?></h5>
						</div>
					</div>
				</div>
				<div class="col">
					<div class="card text-center">
						<h3 class="card-header dead-characters">Dead Characters</h3>
						<div class="card-body bg-light">
							<h5 class="card-title"><?php echo (int) $array['dead']; ?></h5>
						</div>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col col-sm-6">
					<div class="card text-center">
						<h3 class="card-header sexuality">Character Sexuality</h3>
						<div class="card-body bg-light">
							<div>
								<?php ( new Generator() )->make( $this_year, 'chart', 'sexuality' ); ?>
							</div>
						</div>
					</div>
				</div>
				<div class="col col-sm-6">
					<div class="card text-center">
						<h3 class="card-header gender">Character Gender</h3>
						<div class="card-body bg-light">
							<div>
								<?php ( new Generator() )->make( $this_year, 'chart', 'gender' ); ?>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col">
					<div class="card text-center">
						<h3 class="card-header shows-onair">Shows on Air</h3>
						<div class="card-body bg-light">
							<h5 class="card-title"><?php echo (int) $array['shows']; ?></h5>
						</div>
					</div>
				</div>
				<div class="col">
					<div class="card text-center">
						<h3 class="card-header new-shows">New Shows</h3>
						<div class="card-body bg-light">
							<h5 class="card-title"><?php echo (int) $array['started']; ?></h5>
						</div>
					</div>
				</div>
				<div class="col">
					<div class="card text-center">
						<h3 class="card-header canceled-shows">Canceled Shows</h3>
						<div class="card-body bg-light">
							<h5 class="card-title"><?php echo (int) $array['canceled']; ?></h5>
						</div>
					</div>
				</div>
			</div>
		</div>

		<?php
	}
}
