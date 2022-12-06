/*
    This script runs all the various node post install things we need for LTWV.

    1. Downloads FacetWP-CMB2 and CMB2 Grid Code
    2. Copies other files into place
 */

const download = require('download-git-repo');
var copyfiles = require('copyfiles');

// FacetWP + CMB2
// Forked now.
/*
download('WebDevStudios/facetwp-cmb2', 'plugins/facetwp/facetwp-cmb2/', function (err) {
  if (err) return console.log(err);
});
*/

// CMB2 Grid Code
download('origgami/cmb2-grid', 'plugins/cmb2/cmb2-grid/', function (err) {
  if (err) return console.log(err);
});

// CMB Field Select2 (Forked and not used.)
//mustardbees/cmb-field-select2

// Javascript
copyfiles(
    [
        "node_modules/chart.js/dist/chart.min.js",
        "node_modules/chartjs-plugin-annotation/dist/chartjs-plugin-annotation.min.js",
        "node_modules/tablesorter/dist/js/jquery.tablesorter.js",
        "assets/js",
    ],
    { up: true },
    function(err) { if (err) return console.log(err); }
);

// CSS
copyfiles(
    [
        "node_modules/tablesorter/dist/css/theme.bootstrap_4.min.css",
        "assets/css",
    ],
    { up: true },
    function(err) { if (err) return console.log(err); }
);
