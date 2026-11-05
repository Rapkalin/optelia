// Self-hosted typefaces: the CSP allows `font-src 'self'` only, and no visitor
// IP reaches a font CDN. Variable files as Google Fonts serves them to the
// mockup (the mono: one fixed weight), latin subsets only (fonts.css).
import '../styles/fonts.css';
import '@fontsource/ibm-plex-mono/latin-500.css';
import '@fontsource/ibm-plex-mono/latin-ext-500.css';

import '../styles/app.scss';

import { initHeroVideo } from './hero-video';
import { initNavToggle } from './nav-toggle';

initNavToggle();
initHeroVideo();
