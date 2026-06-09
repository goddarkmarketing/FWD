(function () {
    var toggle = document.getElementById('sidebar-toggle');
    var sidebar = document.getElementById('admin-sidebar');
    var overlay = document.getElementById('sidebar-overlay');

    function closeSidebar() {
        if (sidebar) sidebar.classList.remove('is-open');
        if (overlay) overlay.classList.remove('is-visible');
        document.body.style.overflow = '';
    }

    function openSidebar() {
        if (sidebar) sidebar.classList.add('is-open');
        if (overlay) overlay.classList.add('is-visible');
        document.body.style.overflow = 'hidden';
    }

    if (toggle && sidebar) {
        toggle.addEventListener('click', function () {
            if (sidebar.classList.contains('is-open')) {
                closeSidebar();
            } else {
                openSidebar();
            }
        });
    }

    if (overlay) {
        overlay.addEventListener('click', closeSidebar);
    }

    var flash = document.getElementById('admin-flash');
    if (flash) {
        setTimeout(function () {
            flash.classList.add('is-dismissed');
            setTimeout(function () {
                var wrap = flash.closest('.admin-alert-wrap');
                if (wrap) wrap.remove();
            }, 300);
        }, 4500);
    }

    function reindexRepeaterList(list) {
        if (!list) return;
        list.querySelectorAll('.admin-repeater-item').forEach(function (item, idx) {
            item.querySelectorAll('[name]').forEach(function (el) {
                el.name = el.name.replace(/\[\d+\]/, '[' + idx + ']');
            });
            var num = item.querySelector('[data-repeater-index]');
            if (num) num.textContent = String(idx + 1);
        });
    }

    document.querySelectorAll('[data-repeater-add]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var target = document.querySelector(btn.getAttribute('data-repeater-add'));
            if (!target) return;
            var items = target.querySelectorAll('.admin-repeater-item');
            var last = items[items.length - 1];
            if (!last) return;
            var clone = last.cloneNode(true);
            clone.querySelectorAll('input, textarea, select').forEach(function (el) {
                if (el.type === 'checkbox' || el.type === 'radio') {
                    el.checked = false;
                } else {
                    el.value = '';
                }
            });
            target.appendChild(clone);
            reindexRepeaterList(target);
        });
    });

    document.addEventListener('click', function (e) {
        var removeBtn = e.target.closest('[data-repeater-remove]');
        if (!removeBtn) return;
        var item = removeBtn.closest('.admin-repeater-item');
        var list = item && item.parentElement;
        if (item && list && list.querySelectorAll('.admin-repeater-item').length > 1) {
            item.remove();
            reindexRepeaterList(list);
        }
    });

    document.querySelectorAll('.admin-form').forEach(function (form) {
        var actions = form.querySelector('.admin-actions');
        if (actions && !actions.classList.contains('admin-actions--sticky')) {
            var hasPrimary = actions.querySelector('.admin-btn--primary');
            if (hasPrimary && form.querySelectorAll('.admin-card').length > 1) {
                actions.classList.add('admin-actions--sticky');
            }
        }
    });

    function showPreview(wrap, src) {
        if (!wrap) return;
        var img = wrap.querySelector('.admin-img-preview');
        if (!img) return;
        if (!src) {
            img.removeAttribute('src');
            wrap.classList.add('is-hidden');
            return;
        }
        img.src = src;
        wrap.classList.remove('is-hidden');
    }

    function resolvePathUrl(path) {
        path = (path || '').trim();
        if (!path) return '';
        if (/^https?:\/\//i.test(path)) return path;
        var parts = path.replace(/\\/g, '/').replace(/^\/+/, '').split('/');
        return '../' + parts.map(encodeURIComponent).join('/');
    }

    document.querySelectorAll('[data-admin-image-field]').forEach(function (field) {
        var wrap = field.querySelector('[data-admin-preview]');
        var pathInput = field.querySelector('[data-admin-image-path]');
        var fileInput = field.querySelector('[data-admin-image-upload]');

        if (pathInput) {
            pathInput.addEventListener('input', function () {
                showPreview(wrap, resolvePathUrl(pathInput.value));
            });
        }

        if (fileInput) {
            fileInput.addEventListener('change', function () {
                var file = fileInput.files && fileInput.files[0];
                if (!file || !file.type.match(/^image\//)) return;
                var reader = new FileReader();
                reader.onload = function () {
                    showPreview(wrap, reader.result);
                };
                reader.readAsDataURL(file);
            });
        }
    });
})();
