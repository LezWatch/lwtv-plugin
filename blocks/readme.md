This project was bootstrapped with [Create Block](https://developer.wordpress.org/block-editor/handbook/tutorials/create-block/).

It was ported from CGB which has, sadly, been abandoned and throws a lot of deprecated warnings that aren't.

## About

These are Gutenberg Blocks specific to LezWatch.TV Only

## Development Notes

The source code is located in folders within `/src/` - that's where most (if not all) of your work will happen.

When built, the new code will deploy to the `/build/` folder.

The overall code is called from the `/blocks/src/blocks.php` file.

### Notes

Gutenberg is _very_ sensitive to changes, which can invalidate a block and cause it to no longer output properly. Unless you've written in deprecation clauses, be careful when editing.

1. Do _not_ rename any functions
2. Do _not_ change the output

Basically, leave it alone as much as possible.

## Installation and Building

* `$ npm install` - Install and update things.
* `$ npm start` - Starts the build for development.
* `$ npm run build` - Builds the code for production.
* `$ npm run format` - Formats files.
* `$ npm run lint:css` - Lints CSS files.
* `$ npm run lint:js` - Lints JavaScript files.
* `$ npm run packages-update` - Updates WordPress packages to the latest version.
