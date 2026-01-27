/**
 * Theme Manager
 * Handles dark mode, light mode, and auto (system preference) mode
 */

(function() {
    'use strict';

    /**
     * Get system theme preference
     * @returns {string} 'light' or 'dark'
     */
    function getSystemTheme() {
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            return 'dark';
        }
        return 'light';
    }

    /**
     * Resolve theme mode to actual theme
     * @param {string} mode - 'light', 'dark', or 'auto'
     * @returns {string} 'light' or 'dark'
     */
    function resolveTheme(mode) {
        if (mode === 'light' || mode === 'dark') {
            return mode;
        }
        // mode === 'auto'
        return getSystemTheme();
    }

    /**
     * Apply theme to document
     * @param {string} theme - 'light' or 'dark'
     */
    function applyTheme(theme) {
        if (theme === 'light' || theme === 'dark') {
            document.documentElement.setAttribute('data-bs-theme', theme);
            // Store resolved theme in localStorage for quick access
            localStorage.setItem('resolvedTheme', theme);
        }
    }

    /**
     * Get theme mode from data attribute or default to 'auto'
     * @returns {string} Theme mode
     */
    function getThemeMode() {
        var html = document.documentElement;
        var themeMode = html.getAttribute('data-theme-mode');
        return themeMode || 'auto';
    }

    /**
     * Initialize theme on page load
     */
    function initTheme() {
        var themeMode = getThemeMode();
        var resolvedTheme = resolveTheme(themeMode);
        applyTheme(resolvedTheme);
    }

    /**
     * Watch for system theme changes (when mode is 'auto')
     */
    function watchSystemTheme() {
        var themeMode = getThemeMode();
        
        if (themeMode === 'auto' && window.matchMedia) {
            var mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
            
            // Handle change event
            function handleChange(e) {
                var resolvedTheme = e.matches ? 'dark' : 'light';
                applyTheme(resolvedTheme);
            }
            
            // Modern browsers
            if (mediaQuery.addEventListener) {
                mediaQuery.addEventListener('change', handleChange);
            } else {
                // Fallback for older browsers
                mediaQuery.addListener(handleChange);
            }
        }
    }

    // Initialize theme immediately (before DOMContentLoaded to prevent FOUC)
    initTheme();

    // Watch for system changes
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', watchSystemTheme);
    } else {
        watchSystemTheme();
    }

    // Export functions for potential external use
    window.ThemeManager = {
        getSystemTheme: getSystemTheme,
        resolveTheme: resolveTheme,
        applyTheme: applyTheme,
        initTheme: initTheme
    };
})();
