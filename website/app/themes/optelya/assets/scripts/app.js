// Self-hosted typefaces: the CSP allows `font-src 'self'` only, and no visitor
// IP reaches a font CDN. Variable files, as Google Fonts serves them to the
// mockup.
import '@fontsource-variable/archivo/wght.css';
import '@fontsource-variable/ibm-plex-sans/wght.css';
import '@fontsource/ibm-plex-mono/500.css';

import '../styles/app.scss';

import { initHeroVideo } from './hero-video';

initHeroVideo();
