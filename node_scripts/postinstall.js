/*
    This script runs all the various node post install things we need for LTWV.

    1. Downloads CMB2 Grid Code
    2. Copies other files into place
 */

const download = require('download-git-repo');
const { cp } = require('@npmcli/fs');

// FacetWP + CMB2 -- Disabled b/c Forked now.
// WebDevStudios/facetwp-cmb2

// CMB2 Grid Code
download('origgami/cmb2-grid', 'plugins/cmb2/cmb2-grid/', function (err) {
	if (err) return console.log(err);
});

// CMB Field Select2 (Forked and not used.)
// mustardbees/cmb-field-select2

// Move JS
(async () => {
	await cp('node_modules/chart.js/dist/chart.umd.js', 'assets/js/chart.js');
	await cp('node_modules/chartjs-plugin-annotation/dist/chartjs-plugin-annotation.min.js', 'assets/js/chartjs-plugin-annotation.min.js');
	await cp('node_modules/tablesorter/dist/js/jquery.tablesorter.js', 'assets/js/jquery.tablesorter.js');
	console.log('JS files have been moved...');
})();

// Move CSS
(async () => {
	await cp('node_modules/tablesorter/dist/css/theme.bootstrap_4.min.css', 'assets/css/theme.bootstrap_4.min.css');
	console.log('CSS files have been moved...');
})();
