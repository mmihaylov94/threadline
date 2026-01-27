<?php

/**
 * The goal of this file is to allow developers a location
 * where they can overwrite core procedural functions and
 * replace them with their own. This file is loaded during
 * the bootstrap process and is called during the framework's
 * execution.
 *
 * This can be looked at as a `master helper` file that is
 * loaded early on, and may also contain additional functions
 * that you'd like to use throughout your entire application
 *
 * @see: https://codeigniter.com/user_guide/extending/common.html
 */

if (! function_exists('sanitizeHtml')) {
    /**
     * Sanitize HTML content for safe display.
     * Allows safe HTML tags and strips dangerous ones.
     *
     * @param string|null $html The HTML content to sanitize
     * @return string Sanitized HTML
     */
    function sanitizeHtml(?string $html): string
    {
        if (empty($html)) {
            return '';
        }

        // Allowed HTML tags
        $allowedTags = '<p><br><strong><b><em><i><u><s><strike><del><ins><a><ul><ol><li><h1><h2><h3><h4><h5><h6><blockquote><code><pre><img><hr>';

        // Strip all tags except allowed ones
        $html = strip_tags($html, $allowedTags);

        // Remove dangerous attributes using regex
        // Remove onclick, onerror, onload, etc.
        $html = preg_replace('/\s*on\w+\s*=\s*["\'][^"\']*["\']/i', '', $html);
        
        // Remove javascript: URLs from href and src
        $html = preg_replace('/(href|src)\s*=\s*["\']javascript:[^"\']*["\']/i', '', $html);
        
        // Remove data: URLs from href (links) but allow for img src (Quill inline images use data: URLs)
        $html = preg_replace('/href\s*=\s*["\']data:[^"\']*["\']/i', '', $html);

        // Remove style attributes (can contain dangerous CSS)
        $html = preg_replace('/\s*style\s*=\s*["\'][^"\']*["\']/i', '', $html);
        
        // Clean up img tags that lost their src (shouldn't happen now, but safety check)
        $html = preg_replace('/<img[^>]*>\s*<\/img>/i', '', $html);

        // Clean up any empty tags
        $html = preg_replace('/<(\w+)[^>]*>\s*<\/\1>/i', '', $html);

        return trim($html);
    }
}

if (! function_exists('isHtmlContent')) {
    /**
     * Check if content contains HTML tags.
     *
     * @param string|null $content The content to check
     * @return bool True if HTML tags are present
     */
    function isHtmlContent(?string $content): bool
    {
        if (empty($content)) {
            return false;
        }
        return $content !== strip_tags($content);
    }
}

if (! function_exists('get_theme_mode')) {
    /**
     * Get the current user's theme mode preference.
     * Returns 'light', 'dark', or 'auto' (default).
     *
     * @return string Theme mode
     */
    function get_theme_mode(): string
    {
        $session = session();
        
        if (!$session->has('user_id')) {
            return 'auto';
        }

        $profileModel = model(\App\Models\UserProfileModel::class);
        $profile = $profileModel->findByUserId($session->get('user_id'));
        
        if ($profile && isset($profile['theme_mode']) && $profile['theme_mode']) {
            return $profile['theme_mode'];
        }

        return 'auto';
    }
}
