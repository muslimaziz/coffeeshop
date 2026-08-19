import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

const setAppVh = () => {
    const isFullscreen = document.fullscreenElement || window.innerHeight === window.screen.height;
    const avail = window.screen?.availHeight || window.innerHeight;
    const vh = isFullscreen ? window.innerHeight : Math.min(window.innerHeight, avail);
    document.documentElement.style.setProperty('--app-vh', `${vh}px`);
};

window.addEventListener('resize', setAppVh);
document.addEventListener('fullscreenchange', setAppVh);
setAppVh();

Alpine.start();
