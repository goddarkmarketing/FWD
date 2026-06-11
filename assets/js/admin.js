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

    var csrfToken = document.body.getAttribute('data-admin-csrf') || '';
    var uploadUrl = document.body.getAttribute('data-admin-upload-url') || 'media-upload.php';

    function setUploadStatus(el, message, isError) {
        if (!el) return;
        el.textContent = message || '';
        el.classList.toggle('is-error', !!isError);
        el.classList.toggle('is-success', !!message && !isError);
    }

    function uploadImageFile(file, subdir, fixedName) {
        var fd = new FormData();
        fd.append('_csrf', csrfToken);
        fd.append('image', file);
        fd.append('subdir', subdir);
        if (fixedName) {
            fd.append('fixed_name', fixedName);
        }
        return fetch(uploadUrl, { method: 'POST', body: fd, credentials: 'same-origin' })
            .then(function (res) {
                return res.json().then(function (data) {
                    if (!res.ok || !data.ok) {
                        throw new Error((data && data.error) || 'อัปโหลดไม่สำเร็จ');
                    }
                    return data;
                });
            });
    }

    document.querySelectorAll('[data-admin-inline-upload]').forEach(function (input) {
        input.addEventListener('change', function () {
            var files = input.files ? Array.from(input.files) : [];
            if (!files.length) return;

            var field = input.closest('[data-admin-image-field]') || input.closest('[data-admin-gallery-upload]');
            var subdir = input.getAttribute('data-admin-upload-subdir') || 'uploads';
            var fixedName = input.getAttribute('data-admin-upload-fixed') || '';
            var pathInput = field && field.querySelector('[data-admin-image-path]');
            var wrap = field && field.querySelector('[data-admin-preview]');
            var status = field && field.querySelector('[data-admin-upload-status]');
            var reloadOnDone = field && field.hasAttribute('data-admin-reload-on-upload');
            var isMultiple = files.length > 1 || input.hasAttribute('multiple');

            setUploadStatus(status, 'กำลังอัปโหลด…', false);
            input.disabled = true;

            var chain = Promise.resolve();
            var uploaded = 0;
            files.forEach(function (file, index) {
                chain = chain.then(function () {
                    var useFixed = !isMultiple && fixedName ? fixedName : '';
                    return uploadImageFile(file, subdir, useFixed).then(function (data) {
                        uploaded += 1;
                        if (pathInput) {
                            pathInput.value = data.path;
                        }
                        if (wrap && data.preview) {
                            showPreview(wrap, data.preview + (data.preview.indexOf('?') >= 0 ? '&' : '?') + 't=' + Date.now());
                        }
                    });
                });
            });

            chain
                .then(function () {
                    setUploadStatus(status, 'อัปโหลดสำเร็จ ' + uploaded + ' รูป', false);
                    if (reloadOnDone) {
                        setTimeout(function () {
                            window.location.reload();
                        }, 600);
                    }
                })
                .catch(function (err) {
                    setUploadStatus(status, err.message || 'อัปโหลดไม่สำเร็จ', true);
                })
                .finally(function () {
                    input.disabled = false;
                    input.value = '';
                });
        });
    });
})();
