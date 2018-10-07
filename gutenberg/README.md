This project was bootstrapped with [Create Guten Block](https://github.com/ahmadawais/create-guten-block).

## About

These are Gutenberg Blocks specific to LezWatch.TV Only

## Development Notes

The source code is located in folders within `/src/` - that's where most (if not all) of your work will happen.

* `/init.php` : blocks initializer - enqueues all the CSS and JS needed.
* `/blocks.js` : js importer - includes the JS for the build process
* `/screener/block.js` : Screener block

When built, the new code will deploy to the `/dist/` folder.

The overall code is called from the `/gutenberg/src/init.php` file.

### Notes

Gutenberg is _very_ sensitive to changes, which can invalidate a block and cause it to no longer output properly. Unless you've written in deprecation clauses, be careful when editing.

1. Do _not_ rename any functions
2. Do _not_ change the output

Basically, leave it alone as much as possible.

## Installation and Building

1. `npm install` - Install the components you'll need (also for upgrading etc)
2. `npm start` - Use to compile and run the block in development mode (builds non-compressed code in `dist` and remains open)
3. `npm run build` - Use to build production code for your block inside `dist` folder.
