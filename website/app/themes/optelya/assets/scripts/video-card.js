// A video loaded only on demand: the poster link becomes the player, with its
// captions, and keyboard focus moves to it.
export function initVideoCards() {
    document.querySelectorAll('[data-video-card]').forEach((card) => {
        const trigger = card.querySelector('[data-video-play]');

        trigger?.addEventListener('click', (event) => {
            event.preventDefault();

            const video = document.createElement('video');
            video.className = 'video-card__player';
            video.controls = true;
            video.autoplay = true;
            video.preload = 'auto';
            video.src = card.dataset.video;
            video.setAttribute('aria-label', card.dataset.label ?? '');

            if (card.dataset.poster) {
                video.poster = card.dataset.poster;
            }

            if (card.dataset.captions) {
                const track = document.createElement('track');
                track.kind = 'captions';
                track.srclang = 'fr';
                track.label = 'Français';
                track.src = card.dataset.captions;
                track.default = true;
                video.append(track);
            }

            trigger.replaceWith(video);
            video.focus();
        });
    });
}
