/**
 * Gutenberg Blocks
 *
 * All blocks related JavaScript files MUST be imported here in order for
 * Webpack to compile them.
 */

// Common CSS and JS
import './_common/index.js';

// Affiliate Grid Box
import './affiliate-grid/index.js';

// Author Box (CGB originally)
import './author-box/block.js';

// Glossary (CGB originally)
import './glossary/block.js';

// Grade (CGB originally)
import './grade/block.js';

// Screener (CGB originally)
import './screener/block.js';

// TV Shows Calendar (CGB originally)
import './tvshow-calendar/block.js';

// Featured Image
import './featured-image/block.js';

// Pre-Publish Checks
import './pre-publish/block.js';

// Private Note
import './private-note/block.js';

// Spoiler
import './spoiler/block.js';

// Disable fullscreen editor
const isFullscreenMode = wp.data.select( 'core/edit-post' ).isFeatureActive( 'fullscreenMode' );

if ( isFullscreenMode ) {
    wp.data.dispatch( 'core/edit-post' ).toggleFeature( 'fullscreenMode' );
}
