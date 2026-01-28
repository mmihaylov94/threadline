// Threadline Application JavaScript

(function() {
    var COOKIE_CONSENT_KEY = 'threadline_cookie_consent';

    function getEl(id) { return document.getElementById(id); }

    function hideBanner() {
        var banner = getEl('cookie-banner');
        if (banner) {
            banner.classList.add('cookie-banner--hidden');
        }
        try {
            localStorage.setItem(COOKIE_CONSENT_KEY, 'accepted');
        } catch (e) {}
    }

    function initCookieBanner() {
        var banner = getEl('cookie-banner');
        var btn = getEl('cookie-banner-accept');
        if (!banner || !btn) return;
        try {
            if (localStorage.getItem(COOKIE_CONSENT_KEY) === 'accepted') {
                banner.classList.add('cookie-banner--hidden');
                return;
            }
        } catch (e) {}
        banner.classList.remove('cookie-banner--hidden');
        btn.addEventListener('click', hideBanner);
    }

    document.addEventListener('DOMContentLoaded', initCookieBanner);
})();
