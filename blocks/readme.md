These blocks were originally built by CGB which has since been abandoned.

In 2021 they were ported to the official [@wordpress/scripts](https://developer.wordpress.org/block-editor/reference-guides/packages/packages-scripts/) collection of reusable scripts tailored for WordPress development. You do not need to install these! It's already there.

## About

These are Gutenberg Blocks specific to LezWatch.TV!

* `_common` - common JS/CSS snippets
* `_serverside` - Serverside rendering (aka PHP in Gutenberg)
* `featured-image` - Block used by actors/characters/shows to show where the featured image goes, has no impact on display.
* `grade` - A simple 'grade' block to allow for custom 'scores' for a review
* `pre-publish` - A backend only 'block' that checks for requirements before you're allowed to post.
* `screener` - A block to leave a screener review
* `spoiler` - Show a spoiler warning, with custom design.

## Development

The source code is located in folders within `/src/` - that's where most (if not all) of your work will happen. Each new block gets a folder and in each folder there must be a `block.json` file that stores all the metadata. Read [Metadata in block.json](https://developer.wordpress.org/block-editor/reference-guides/block-api/block-metadata/) if this is your first rodeo.

The blocks will automagically build anytime anyone runs `npm run build` from the main folder. You can also run `npm run build` from the `blocks` folder.

All JS and CSS from blocks defined in `blocks/*/block.json` get pushed to the `blocks/build/` folder via the build process. PHP scans this directory and registers blocks in `php/class-blocks.php`. The overall code is called from the `/blocks/src/blocks.php` file.

The subfolders are _NOT_ stored in Git, because they're not needed to be. We run the build via actions 


### Getting Started

From the main plugin folder, run `npm install`

This will install everything you need to go!

### Adding Blocks

Add a new block to the `/blocks/` directory, re-run the build process, and your new block will be available in the editor.

Blocks must have:

- A dedicated directory (such as `hello-world`) under this `blocks` directory,
- [A metadata file](https://developer.wordpress.org/block-editor/reference-guides/block-api/block-metadata/) named `block.json`.
- An editor file named `block.js` which does `registerBlockType()` for the block.

The file paths in `block.json` for `script`, `editorScript`, `style`, `editorStyle` and `viewScript` are used for the following:

1. By `@wordpress/scripts` build tooling [to determine the Webpack JS entry points](https://github.com/WordPress/gutenberg/blob/8dcb185deb5ac7f8c4cf0de32962023be3ef9d3e/packages/scripts/utils/config.js#L206-L281) -- files with extension matching `[jt]s?(x)` are used as Webpack entry points. **Only the CSS imported via the JS files is processed!**

2. To register and enqueue scripts and styles for the block on the frontend output and in the block editor. The file paths must be relative to the `blocks/build/*` directories and match the [naming conventions used by the `@wordpress/scripts` Webpack config](https://github.com/WordPress/gutenberg/blob/60bddd382c2b66c1c9ccd726717087a376958fa2/docs/reference-guides/block-api/block-metadata.md):

   - The compiled JS file names match the source file names. For example, `blocks/example/script.js` is compiled into `blocks/build/example/script.js`. Files referenced in `editorScript` of `block.json` are enqueued only in the block editor while files in `viewScript` are enqueued only in the frontend (this property only exists in WP 5.9 or newer. [Read more](https://developer.wordpress.org/block-editor/reference-guides/block-api/block-metadata/#view-script)). Files in `script` are enqueued in _both_ the editor and the frontend.

   - The [`@wordpress/scripts` Webpack config](https://github.com/WordPress/gutenberg/blob/8dcb185deb5ac7f8c4cf0de32962023be3ef9d3e/packages/scripts/config/webpack.config.js#L108-L122) extracts CSS imports in `example.js` with file names matching `style(\.module)?\.(sc|sa|c)ss` and bundles them into `style-example.css`. This file can be referenced in `style` of `block.json` to be included in the editor and frontend output of the block. All other CSS imports in `example.js` are bundled into `example.css` and should be used for `editorStyle`.

     For example, an `block.js` file with the following imports:

     ```js
     import "./editor.scss";
     import "./header.scss";
     import "./style.module.scss";
     import "./style.scss";
     ```

     produces the following files during the build:

     - `editor.css` - contains the CSS from `editor.scss` and `header.scss`
     - `style-editor.css` - contains the CSS from `style.module.scss` and `style.scss`

Each block must have at _least_ the `block.js` file (also referenced in `editorScript` of `block.json`) to register the block via `registerBlockType()`.

For example, a block directory with the following files:

- `block.js` importing `editor.scss` (backend-only styles),
- `script.js` importing `style.scss` (both backend and frontend styles)

will produce the following files during the build:

- `block.js`
- `editor.css`
- `script.js`
- `style-script.js`

which should be registered in `block.json` as follows:

```json
{
	"editorScript": "file:block.js",
	"editorStyle": "file:editor.css",
	"script": "file:script.js",
	"style": "file:style-script.css"
}
```

For blocks that do not need any JS for the frontend output, create _only_ `block.js` that imports `style.scss` (for both frontend and backend styles), which produce the following files during the build:

- `block.js`
- `style-block.js`

that can be registered as follows:

```json
{
	"editorScript": "file:block.js",
	"style": "file:style-editor.css"
}
```

### ServerSideRendering

Due to a weird quirk in ServerSideRender, you can't (yet) use a `block.json` file to register each server side instance. In order to work around this, there is a separate psudeo-block called `_serverside`, and inside there are some blocks that just don't work any other way.

This includes:

* Author Box (aka Team Member)
* Glossary
* Private Note
* TV Show Calendar

It is managed by the `block.js` file, which imports all the sub-blocks.

### Warning

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
