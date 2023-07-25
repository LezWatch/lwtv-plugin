# Built Assets for Blocks

All JS and CSS from blocks defined in `blocks/*/block.json` get ejected here during the build process. PHP scans this directory and registers blocks in `php/class-blocks.php`.

The subfolders are _NOT_ stored in Git, becuase they're not needed to be. We run the build via actions 
