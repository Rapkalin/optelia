// Hero loop: plays only when the visitor has not asked for reduced motion
// (zoning v2, decision 12). Without this script, the poster image stays.
// Labels come from the template (data-label-*), where they are translatable.
export function initHeroVideo() {
    const video = document.querySelector('[data-hero-video]');
    const button = document.querySelector('[data-hero-pause]');

    if (!video || !button) {
        return;
    }

    // The label says what the button does; `data-paused` swaps the icon.
    const setPlaying = (isPlaying) => {
        button.setAttribute('aria-label', isPlaying ? button.dataset.labelPause : button.dataset.labelPlay);
        button.toggleAttribute('data-paused', !isPlaying);
    };

    button.addEventListener('click', () => {
        if (video.paused) {
            video.play();
        } else {
            video.pause();
        }
    });
    video.addEventListener('play', () => setPlaying(true));
    video.addEventListener('pause', () => setPlaying(false));

    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        setPlaying(false);

        return;
    }

    video.play().catch(() => setPlaying(false));
}
