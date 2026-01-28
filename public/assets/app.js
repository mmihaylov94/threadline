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

// Report Modal Handling
(function() {
    var reportModal = document.getElementById('reportModal');
    if (reportModal) {
        var reportForm = document.getElementById('reportForm');
        var contentTypeInput = document.getElementById('report-content-type');
        var contentIdInput = document.getElementById('report-content-id');
        var lastClickedButton = null;
        
        // Store button data when clicked
        document.addEventListener('click', function(e) {
            var button = e.target.closest('[data-bs-toggle="modal"][data-bs-target="#reportModal"]');
            if (button) {
                lastClickedButton = button;
            }
        });
        
        reportModal.addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget || lastClickedButton;
            
            if (button) {
                var contentType = button.getAttribute('data-content-type');
                var contentId = button.getAttribute('data-content-id');
                var contentSlug = button.getAttribute('data-content-slug');
                
                if (contentTypeInput) contentTypeInput.value = contentType || '';
                if (contentIdInput) contentIdInput.value = contentId || '';
                
                // Set form action based on content type
                if (reportForm) {
                    // Use absolute paths from site root
                    if (contentType === 'thread') {
                        // Use slug for threads - it's required!
                        if (!contentSlug) {
                            console.error('Thread slug not found for report form. Button:', button);
                            return;
                        }
                        reportForm.action = '/threads/' + contentSlug + '/report';
                    } else if (contentType === 'post') {
                        // Use ID for posts
                        reportForm.action = '/posts/' + contentId + '/report';
                    }
                }
            }
        });
        
        if (reportForm) {
            reportForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                var checkboxes = reportForm.querySelectorAll('input[name="guideline_violations[]"]:checked');
                if (checkboxes.length === 0) {
                    alert('Please select at least one guideline violation.');
                    return false;
                }
                
                var formData = new FormData(reportForm);
                
                fetch(reportForm.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        var modal = bootstrap.Modal.getInstance(reportModal);
                        if (modal) modal.hide();
                        
                        // Show success message
                        var alertDiv = document.createElement('div');
                        alertDiv.className = 'alert alert-success alert-dismissible fade show';
                        alertDiv.innerHTML = '<strong>Success!</strong> ' + (data.message || 'Report submitted successfully.') + 
                            '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
                        document.querySelector('main').insertBefore(alertDiv, document.querySelector('main').firstChild);
                        
                        // Reset form
                        reportForm.reset();
                    } else {
                        alert(data.error || 'Could not submit the report. Please try again.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while submitting the report. Please try again.');
                });
            });
        }
    }
})();

// Sidebar Toggle
(function() {
    var sidebar = document.getElementById('forum-sidebar');
    var sidebarToggle = document.getElementById('sidebar-toggle');
    var SIDEBAR_STATE_KEY = 'threadline_sidebar_state';
    
    if (sidebar && sidebarToggle) {
        // Load saved state
        try {
            var savedState = localStorage.getItem(SIDEBAR_STATE_KEY);
            if (savedState === 'collapsed') {
                sidebar.classList.add('forum-sidebar--collapsed');
            }
        } catch (e) {}
        
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('forum-sidebar--collapsed');
            
            try {
                if (sidebar.classList.contains('forum-sidebar--collapsed')) {
                    localStorage.setItem(SIDEBAR_STATE_KEY, 'collapsed');
                } else {
                    localStorage.setItem(SIDEBAR_STATE_KEY, 'expanded');
                }
            } catch (e) {}
        });
    }
})();

// Recently viewed threads (localStorage, per user)
(function() {
    var RECENTS_KEY_PREFIX = 'threadline_recent_threads_user_';
    var MAX_RECENTS = 5;

    function getUserId() {
        var body = document.body;
        if (!body) return null;
        var raw = body.getAttribute('data-user-id');
        if (!raw) return null;
        var n = parseInt(raw, 10);
        return Number.isFinite(n) ? n : null;
    }

    function storageKey() {
        var userId = getUserId();
        if (!userId) return null;
        return RECENTS_KEY_PREFIX + userId;
    }

    function readRecents() {
        var key = storageKey();
        if (!key) return [];
        try {
            var raw = localStorage.getItem(key);
            if (!raw) return [];
            var arr = JSON.parse(raw);
            return Array.isArray(arr) ? arr : [];
        } catch (e) {
            return [];
        }
    }

    function writeRecents(items) {
        var key = storageKey();
        if (!key) return;
        try {
            localStorage.setItem(key, JSON.stringify(items));
        } catch (e) {}
    }

    function addRecent(item) {
        if (!item || !item.slug || !item.title) return;
        var items = readRecents();
        items = items.filter(function(x) { return x && x.slug !== item.slug; });
        items.unshift({
            slug: item.slug,
            title: item.title,
            category: item.category || ''
        });
        items = items.slice(0, MAX_RECENTS);
        writeRecents(items);
    }

    function renderRecents() {
        var list = document.getElementById('recently-viewed-list');
        var empty = document.getElementById('recently-viewed-empty');
        if (!list || !empty) return;

        var items = readRecents();
        list.innerHTML = '';

        if (!items.length) {
            empty.style.display = '';
            return;
        }

        empty.style.display = 'none';
        items.forEach(function(item) {
            var li = document.createElement('li');
            li.className = 'forum-sidebar__item';

            var a = document.createElement('a');
            a.className = 'forum-sidebar__link';
            a.href = '/threads/' + encodeURIComponent(item.slug);
            a.textContent = item.title;

            li.appendChild(a);
            if (item.category) {
                var meta = document.createElement('span');
                meta.className = 'forum-sidebar__meta';
                meta.textContent = item.category;
                li.appendChild(meta);
            }
            list.appendChild(li);
        });
    }

    // Track on thread view page (expects data attributes)
    document.addEventListener('DOMContentLoaded', function() {
        renderRecents();

        var threadEl = document.querySelector('[data-thread-slug][data-thread-title]');
        if (threadEl) {
            addRecent({
                slug: threadEl.getAttribute('data-thread-slug'),
                title: threadEl.getAttribute('data-thread-title'),
                category: threadEl.getAttribute('data-thread-category') || ''
            });
            renderRecents();
        }
    });
})();

// Voting (threads + posts)
(function() {
    function postForm(url, dataObj) {
        var formData = new FormData();
        Object.keys(dataObj).forEach(function(k) { formData.append(k, dataObj[k]); });
        return fetch(url, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        }).then(function(r) { return r.json(); });
    }

    function getCsrf() {
        // Grab CSRF token+hash from any existing form field
        var input = document.querySelector('input[name][value][type="hidden"]');
        if (!input) return null;
        return { name: input.name, value: input.value };
    }

    document.addEventListener('click', function(e) {
        var btn = e.target.closest('.thread-vote .vote-btn, .reply-vote .vote-btn');
        if (!btn) return;

        var csrf = getCsrf();
        if (!csrf) return;

        var vote = btn.getAttribute('data-vote'); // up/down
        var container = btn.closest('.thread-vote, .reply-vote');
        if (!container) return;

        // Get current user vote from data attribute
        var currentVote = parseInt(container.getAttribute('data-user-vote') || '0', 10);
        
        // Toggle behavior: if clicking the same button, remove vote; otherwise switch
        var voteValue = vote;
        if (vote === 'up' && currentVote === 1) {
            voteValue = 'remove'; // Remove upvote
        } else if (vote === 'down' && currentVote === -1) {
            voteValue = 'remove'; // Remove downvote
        }

        var url = null;
        if (container.classList.contains('thread-vote')) {
            var slug = container.getAttribute('data-thread-slug');
            url = '/threads/' + encodeURIComponent(slug) + '/vote';
        } else {
            var postId = container.getAttribute('data-post-id');
            url = '/posts/' + encodeURIComponent(postId) + '/vote';
        }

        var payload = {};
        payload[csrf.name] = csrf.value;
        payload['vote'] = voteValue;

        postForm(url, payload).then(function(data) {
            if (!data || !data.success) return;
            
            // Update score
            var scoreEl = container.querySelector('[data-role="score"]');
            if (scoreEl) scoreEl.textContent = String(data.score);
            
            // Update user vote state
            var userVote = parseInt(data.userVote || '0', 10);
            container.setAttribute('data-user-vote', String(userVote));
            
            // Update button highlighting
            var upBtn = container.querySelector('.vote-btn--up');
            var downBtn = container.querySelector('.vote-btn--down');
            
            if (upBtn) {
                if (userVote === 1) {
                    upBtn.classList.add('vote-btn--active');
                } else {
                    upBtn.classList.remove('vote-btn--active');
                }
            }
            
            if (downBtn) {
                if (userVote === -1) {
                    downBtn.classList.add('vote-btn--active');
                } else {
                    downBtn.classList.remove('vote-btn--active');
                }
            }
        }).catch(function(err) {
            console.error(err);
        });
    });
})();
