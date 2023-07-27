/*
    This script runs all the various node post install things we need for LTWV.

    * Copies files into place
 */

const { cp } = require('@npmcli/fs');

// Move JS
(async () => {
	await cp('node_modules/chart.js/dist/chart.umd.js', 'assets/js/chart.js');
	await cp('node_modules/chart.js/dist/chart.umd.js.map', 'assets/js/chart.umd.js.map');
	await cp('node_modules/chartjs-plugin-annotation/dist/chartjs-plugin-annotation.min.js', 'assets/js/chartjs-plugin-annotation.min.js');
	await cp('node_modules/tablesorter/dist/js/jquery.tablesorter.js', 'assets/js/jquery.tablesorter.js');
	console.log('JS files have been moved...');
})();

// Move CSS
(async () => {
	await cp('node_modules/tablesorter/dist/css/theme.bootstrap_4.min.css', 'assets/css/theme.bootstrap_4.min.css');
	console.log('CSS files have been moved...');
})();

