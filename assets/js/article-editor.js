(function () {

    var form = document.getElementById('article-form');

    var editorEl = document.getElementById('article-editor');

    if (!form || !editorEl) return;



    var uploadUrl = form.getAttribute('data-article-upload') || '';

    var csrfInput = form.querySelector('input[name="_csrf"]');



    function getInsertIndex(quill) {

        var range = quill.getSelection();

        if (range) return range.index;

        quill.focus();

        range = quill.getSelection(true);

        return range ? range.index : quill.getLength();

    }



    function normalizeContentImages(html) {

        return html.replace(/<img([^>]*)\ssrc=["']([^"']+)["']/gi, function (match, attrs, src) {

            var path = src.trim();

            if (/^https?:\/\//i.test(path)) {

                var assetMatch = path.match(/\/(assets\/[^?#]+)/i);

                if (assetMatch) path = assetMatch[1];

            }

            path = path.replace(/^\.\.\//, '').replace(/^\//, '');

            return '<img' + attrs + ' src="' + path + '"';

        });

    }



    function insertImage(quill, previewSrc, storedPath) {

        var index = getInsertIndex(quill);

        quill.insertEmbed(index, 'image', previewSrc, 'user');

        if (storedPath) {

            var imgs = quill.root.querySelectorAll('img');

            var img = imgs[imgs.length - 1];

            if (img) img.setAttribute('data-asset-path', storedPath);

        }

        quill.setSelection(index + 1, 0);

    }



    function createImagePicker(onInsert) {

        var wrap = document.createElement('div');

        wrap.className = 'article-image-picker';

        wrap.hidden = true;

        wrap.innerHTML =

            '<div class="article-image-picker__head">' +

            '<strong>แทรกรูปภาพ</strong>' +

            '<button type="button" class="article-image-picker__close" aria-label="ปิด">&times;</button>' +

            '</div>' +

            '<div class="article-image-picker__body">' +

            '<label class="article-image-picker__upload">' +

            '<span class="admin-btn admin-btn--outline admin-btn--sm">เลือกไฟล์จากเครื่อง</span>' +

            '<input type="file" accept="image/jpeg,image/png,image/webp,image/gif" hidden>' +

            '</label>' +

            '<p class="form-hint">หรือวาง path จาก <a href="media.php" target="_blank" rel="noopener">สื่อ &amp; รูปภาพ</a></p>' +

            '<div class="article-image-picker__url-row">' +

            '<input type="text" class="article-image-picker__url" placeholder="assets/uploads/photo.jpg">' +

            '<button type="button" class="admin-btn admin-btn--primary admin-btn--sm">แทรก</button>' +

            '</div>' +

            '<p class="article-image-picker__status form-hint" hidden></p>' +

            '</div>';



        var fileInput = wrap.querySelector('input[type="file"]');

        var urlInput = wrap.querySelector('.article-image-picker__url');

        var urlBtn = wrap.querySelector('.article-image-picker__url-row button');

        var statusEl = wrap.querySelector('.article-image-picker__status');

        var closeBtn = wrap.querySelector('.article-image-picker__close');



        function setStatus(msg, isError) {

            if (!statusEl) return;

            statusEl.hidden = !msg;

            statusEl.textContent = msg || '';

            statusEl.style.color = isError ? '#dc2626' : '';

        }



        function hide() {

            wrap.hidden = true;

            setStatus('');

            if (fileInput) fileInput.value = '';

            if (urlInput) urlInput.value = '';

        }



        function show(anchor) {

            wrap.hidden = false;

            var parent = editorEl.closest('.admin-card') || editorEl.parentNode;

            if (wrap.parentNode !== parent) parent.appendChild(wrap);

            if (anchor && anchor.getBoundingClientRect) {

                var rect = anchor.getBoundingClientRect();

                var parentRect = parent.getBoundingClientRect();

                wrap.style.top = (rect.bottom - parentRect.top + 8) + 'px';

                wrap.style.left = Math.max(0, rect.left - parentRect.left) + 'px';

            }

            setStatus('');

        }



        closeBtn.addEventListener('click', hide);

        document.addEventListener('click', function (e) {

            if (wrap.hidden) return;

            if (wrap.contains(e.target) || e.target.closest('.ql-image')) return;

            hide();

        });



        fileInput.addEventListener('change', function () {

            var file = fileInput.files && fileInput.files[0];

            if (!file) return;

            if (!uploadUrl) {

                setStatus('ไม่พบ URL อัปโหลด', true);

                return;

            }

            if (!csrfInput || !csrfInput.value) {

                setStatus('เซสชันหมดอายุ กรุณารีเฟรชหน้า', true);

                return;

            }



            var body = new FormData();

            body.append('image', file);

            body.append('_csrf', csrfInput.value);



            setStatus('กำลังอัปโหลด...');

            fetch(uploadUrl, { method: 'POST', body: body, credentials: 'same-origin' })

                .then(function (res) { return res.json().then(function (data) { return { res: res, data: data }; }); })

                .then(function (result) {

                    if (!result.data || !result.data.ok) {

                        throw new Error((result.data && result.data.error) || 'อัปโหลดไม่สำเร็จ');

                    }

                    onInsert(result.data.preview || result.data.path, result.data.path);

                    hide();

                })

                .catch(function (err) {

                    setStatus(err.message || 'อัปโหลดไม่สำเร็จ', true);

                });

        });



        urlBtn.addEventListener('click', function () {

            var path = urlInput.value.trim();

            if (!path) {

                setStatus('กรุณาใส่ path รูปภาพ', true);

                return;

            }

            var preview = path.indexOf('../') === 0 || /^https?:\/\//i.test(path) ? path : '../' + path.replace(/^\//, '');

            onInsert(preview, path.replace(/^\.\.\//, '').replace(/^\//, ''));

            hide();

        });



        return { show: show, hide: hide };

    }



    if (typeof Quill === 'undefined') {

        editorEl.setAttribute('contenteditable', 'true');

        editorEl.classList.add('article-editor--fallback');

        var warn = document.createElement('p');

        warn.className = 'form-hint';

        warn.style.color = '#dc2626';

        warn.textContent = 'โหลด Quill Editor ไม่สำเร็จ — ใช้โหมดพิมพ์ HTML โดยตรงชั่วคราว';

        editorEl.parentNode.insertBefore(warn, editorEl);

        form.addEventListener('submit', function () {

            var contentInput = document.getElementById('content-input');

            if (contentInput) contentInput.value = editorEl.innerHTML;

        });

        return;

    }



    var initialHtml = editorEl.innerHTML;

    editorEl.innerHTML = '';



    var quill = new Quill('#article-editor', {

        theme: 'snow',

        placeholder: 'เริ่มเขียนบทความ...',

        modules: {

            toolbar: [

                [{ header: [2, 3, 4, false] }],

                ['bold', 'italic', 'underline', 'strike'],

                [{ list: 'ordered' }, { list: 'bullet' }],

                [{ indent: '-1' }, { indent: '+1' }],

                [{ align: [] }],

                ['blockquote', 'code-block'],

                ['link', 'image'],

                ['clean'],

            ],

        },

    });



    if (initialHtml && initialHtml.trim() !== '') {

        quill.clipboard.dangerouslyPasteHTML(initialHtml);

        quill.root.querySelectorAll('img[src^="assets/"]').forEach(function (img) {

            var path = img.getAttribute('src');

            img.setAttribute('data-asset-path', path);

            img.setAttribute('src', '../' + path);

        });

    }



    var contentInput = document.getElementById('content-input');

    var titleInput = document.getElementById('title');

    var slugInput = document.getElementById('slug');

    var excerptInput = document.getElementById('excerpt');

    var metaTitleInput = document.getElementById('meta_title');

    var metaDescInput = document.getElementById('meta_description');



    var imagePicker = createImagePicker(function (previewSrc, storedPath) {

        insertImage(quill, previewSrc, storedPath);

    });



    function textLen(el) {

        return el && el.value ? el.value.trim().length : 0;

    }



    function updateCharHints() {

        document.querySelectorAll('.char-hint').forEach(function (hint) {

            var id = hint.getAttribute('data-for');

            var field = document.getElementById(id);

            if (!field) return;

            var len = textLen(field);

            var min = parseInt(hint.getAttribute('data-min') || '0', 10);

            var max = parseInt(hint.getAttribute('data-max') || '0', 10);

            var msg = len + ' ตัวอักษร';

            if (min > 0 && max > 0) msg = len + ' ตัวอักษร (แนะนำ ' + min + '–' + max + ')';

            else if (max > 0) msg += ' / แนะนำไม่เกิน ' + max;

            hint.textContent = msg;

            hint.classList.toggle('is-warn', (max > 0 && len > max) || (min > 0 && len > 0 && len < min));

        });

    }



    function slugify(text) {

        return text.toLowerCase().trim()

            .replace(/[^a-z0-9\-]+/g, '-')

            .replace(/^-+|-+$/g, '');

    }



    function updateSeoPreview() {

        var title = metaTitleInput && metaTitleInput.value.trim() ? metaTitleInput.value.trim() : (titleInput ? titleInput.value.trim() : '');

        var desc = metaDescInput && metaDescInput.value.trim() ? metaDescInput.value.trim() : (excerptInput ? excerptInput.value.trim() : '');

        var slug = slugInput ? slugInput.value.trim() : '';



        var titleEl = document.getElementById('seo-preview-title');

        var descEl = document.getElementById('seo-preview-desc');

        var slugEl = document.getElementById('seo-preview-slug');

        var slugPreview = document.getElementById('slug-preview');



        if (titleEl) titleEl.textContent = title || 'หัวข้อบทความ';

        if (descEl) descEl.textContent = desc || 'คำอธิบายจะแสดงที่นี่...';

        if (slugEl) slugEl.textContent = slug || 'slug';

        if (slugPreview) slugPreview.textContent = slug || 'your-slug';

    }



    if (titleInput) {

        titleInput.addEventListener('input', function () {

            if (slugInput && !slugInput.dataset.touched) {

                slugInput.value = slugify(titleInput.value);

            }

            updateSeoPreview();

            updateCharHints();

        });

    }



    if (slugInput) {

        slugInput.addEventListener('input', function () {

            slugInput.dataset.touched = '1';

            updateSeoPreview();

        });

    }



    [excerptInput, metaTitleInput, metaDescInput].forEach(function (el) {

        if (!el) return;

        el.addEventListener('input', function () {

            updateSeoPreview();

            updateCharHints();

        });

    });



    var toolbar = quill.getModule('toolbar');

    if (toolbar) {

        toolbar.addHandler('image', function () {

            var imageBtn = editorEl.parentNode.querySelector('.ql-image');

            imagePicker.show(imageBtn || editorEl);

        });

    }



    form.addEventListener('submit', function () {

        if (contentInput) {

            quill.root.querySelectorAll('img[data-asset-path]').forEach(function (img) {

                img.setAttribute('src', img.getAttribute('data-asset-path'));

                img.removeAttribute('data-asset-path');

            });

            contentInput.value = normalizeContentImages(quill.root.innerHTML);

        }

        if (slugInput && !slugInput.value.trim() && titleInput) {

            slugInput.value = slugify(titleInput.value);

        }

    });



    updateSeoPreview();

    updateCharHints();

})();


