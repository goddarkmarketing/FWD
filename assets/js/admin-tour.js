/**
 * Interactive admin guided tours — resumes across page navigations.
 */
(function () {
    var STORAGE_KEY = 'fwdAdminTour';

    var TOURS = {
        'add-plan': {
            title: 'เพิ่มแผนประกัน',
            steps: [
                {
                    page: 'catalog.php',
                    selector: '[data-tour="nav-catalog"]',
                    title: 'เปิดเมนูแคตตาล็อก',
                    text: 'เมนูนี้คือรายการแผนทั้งหมดที่แสดงเป็นการ์ดสินค้าบนเว็บไซต์ กดที่เมนูนี้เพื่อเริ่มเพิ่มแผนใหม่'
                },
                {
                    page: 'catalog.php',
                    selector: '[data-tour="catalog-add-btn"]',
                    title: 'กดปุ่ม + เพิ่มแผน',
                    text: 'ปุ่มส้มมุมขวาบนใช้สำหรับสร้างแผนใหม่ ขั้นตอนถัดไปจะพาไปหน้าฟอร์มกรอกข้อมูล'
                },
                {
                    page: 'catalog-add.php',
                    selector: '[data-tour="add-slug"]',
                    title: 'กรอก Slug แผน',
                    text: 'Slug คือรหัสภาษาอังกฤษของแผน เช่น my-new-plan ใช้ตัวพิมพ์เล็ก a-z ตัวเลข และเครื่องหมาย - เท่านั้น',
                    go: 'catalog-add.php'
                },
                {
                    page: 'catalog-add.php',
                    selector: '[data-tour="add-category"]',
                    title: 'เลือกหมวดหมู่',
                    text: 'เลือกหมวดที่ต้องการให้แผนไปแสดง เช่น ประกันสุขภาพ ชีวิต หรือโรคร้ายแรง'
                },
                {
                    page: 'catalog-add.php',
                    selector: '[data-tour="add-title"]',
                    title: 'กรอกชื่อแผน',
                    text: 'ชื่อนี้คือชื่อที่ลูกค้าจะเห็นบนการ์ดสินค้า ควรสั้น ชัด และเข้าใจง่าย'
                },
                {
                    page: 'catalog-add.php',
                    selector: '[data-tour="add-desc"]',
                    title: 'คำอธิบายและรายละเอียดอื่น',
                    text: 'ใส่คำอธิบายสั้น ๆ ส่วนลด แท็ก หรือรูปภาพได้ตามต้องการ ไม่บังคับทุกช่อง'
                },
                {
                    page: 'catalog-add.php',
                    selector: '[data-tour="add-submit"]',
                    title: 'กดบันทึก “เพิ่มแผน”',
                    text: 'เมื่อข้อมูลครบ กดปุ่มนี้เพื่อบันทึก แผนจะขึ้นบนเว็บ PHP ทันที จากนั้นรีเฟรชหน้าเว็บ (Ctrl+F5) เพื่อตรวจผล'
                }
            ]
        },
        'remove-plan': {
            title: 'ยกเลิก / ซ่อนแผน',
            steps: [
                {
                    page: 'catalog.php',
                    selector: '[data-tour="nav-catalog"]',
                    title: 'เปิดเมนูแคตตาล็อก',
                    text: 'ไปที่แคตตาล็อกเพื่อหาแผนที่ต้องการเอาออกจากเว็บไซต์'
                },
                {
                    page: 'catalog.php',
                    selector: '[data-tour="catalog-search"]',
                    title: 'ค้นหาแผน',
                    text: 'พิมพ์ชื่อแผนหรือ slug ในช่องค้นหา เพื่อหาแผนจากรายการยาว ๆ ได้เร็วขึ้น'
                },
                {
                    page: 'catalog.php',
                    selector: '[data-tour="catalog-delete-btn"]',
                    title: 'กดปุ่ม “ลบ”',
                    text: 'ปุ่มแดงใช้เอาแผนออกจากเว็บ ระบบจะถามยืนยันก่อนทำงาน'
                },
                {
                    page: 'catalog.php',
                    selector: '[data-tour="catalog-status"]',
                    title: 'สถานะแผน',
                    text: 'แผนเดิมของระบบเมื่อลบแล้วจะเป็น “ซ่อนอยู่” (กู้คืนได้) ส่วนแผนที่เพิ่มเองจะถูกลบถาวร'
                },
                {
                    page: 'catalog.php',
                    selector: '[data-tour="catalog-unhide-btn"], [data-tour="catalog-delete-btn"]',
                    title: 'กู้คืนได้ถ้าต้องการ',
                    text: 'ถ้าแผนถูกซ่อนไว้ จะมีปุ่ม “แสดงอีกครั้ง” เพื่อนำกลับขึ้นเว็บ ถ้ายังไม่ซ่อน ปุ่มลบคือจุดเริ่มต้นของขั้นตอนนี้'
                }
            ]
        },
        'promotions': {
            title: 'แก้ไขโปรโมชัน',
            steps: [
                {
                    page: 'promotions.php',
                    selector: '[data-tour="nav-promotions"]',
                    title: 'เปิดเมนูโปรโมชัน',
                    text: 'เมนูนี้จัดการข้อเสนอพิเศษที่แสดงบนหน้าแรกและหน้าโปรโมชัน'
                },
                {
                    page: 'promotions.php',
                    selector: '[data-tour="promo-home-count"]',
                    title: 'จำนวนบนหน้าแรก',
                    text: 'ตัวเลขนี้กำหนดว่าหน้าแรกโชว์กี่โปรโมชัน ไม่ได้จำกัดจำนวนรายการทั้งหมดด้านล่าง'
                },
                {
                    page: 'promotions.php',
                    selector: '[data-tour="promo-add-btn"]',
                    title: 'เพิ่มโปรโมชัน',
                    text: 'กดปุ่มนี้เพื่อสร้างรายการใหม่ หรือกด “แก้ไข” ในตารางเพื่อเปลี่ยนวันที่ / หัวข้อ'
                },
                {
                    page: 'promotions.php',
                    selector: '[data-tour="promo-edit-btn"]',
                    title: 'แก้ไขรายการ',
                    text: 'กดแก้ไขเพื่อเปิดหน้าฟอร์ม เปลี่ยนข้อความวันที่ เช่น “ถึง 31 ธ.ค. 2026” แล้วกดบันทึก'
                }
            ]
        }
    };

    function getState() {
        try {
            return JSON.parse(sessionStorage.getItem(STORAGE_KEY) || 'null');
        } catch (e) {
            return null;
        }
    }

    function setState(state) {
        if (!state) {
            sessionStorage.removeItem(STORAGE_KEY);
            return;
        }
        sessionStorage.setItem(STORAGE_KEY, JSON.stringify(state));
    }

    function currentPage() {
        var path = window.location.pathname || '';
        var file = path.split('/').pop() || '';
        return file.split('?')[0] || 'index.php';
    }

    function adminUrl(file) {
        var path = window.location.pathname || '';
        var base = path.replace(/\/[^\/]*$/, '/');
        return base + file;
    }

    function clearUi() {
        document.querySelectorAll('.admin-tour-highlight').forEach(function (el) {
            el.classList.remove('admin-tour-highlight');
        });
        var overlay = document.getElementById('admin-tour-overlay');
        var tip = document.getElementById('admin-tour-tip');
        if (overlay) overlay.remove();
        if (tip) tip.remove();
    }

    function positionTip(tip, target) {
        var rect = target.getBoundingClientRect();
        var tipRect = tip.getBoundingClientRect();
        var gap = 14;
        var top = rect.bottom + gap;
        var left = rect.left;

        if (top + tipRect.height > window.innerHeight - 12) {
            top = rect.top - tipRect.height - gap;
        }
        if (top < 12) top = 12;
        if (left + tipRect.width > window.innerWidth - 12) {
            left = window.innerWidth - tipRect.width - 12;
        }
        if (left < 12) left = 12;

        tip.style.top = top + 'px';
        tip.style.left = left + 'px';
    }

    function endTour(showDone) {
        var state = getState();
        var title = state && TOURS[state.id] ? TOURS[state.id].title : 'ทัวร์แนะนำ';
        clearUi();
        setState(null);
        if (showDone) {
            showDoneModal(title);
        }
    }

    function showDoneModal(title) {
        var wrap = document.createElement('div');
        wrap.id = 'admin-tour-done';
        wrap.innerHTML =
            '<div class="admin-tour-done__card">' +
            '<div class="admin-tour-done__icon">✓</div>' +
            '<h3>จบขั้นตอนแล้ว</h3>' +
            '<p>ทัวร์ “' + escapeHtml(title) + '” เสร็จสมบูรณ์ คุณสามารถลองทำจริงได้เลย</p>' +
            '<div class="admin-tour-done__actions">' +
            '<a class="admin-btn admin-btn--outline" href="' + adminUrl('manual.php') + '">กลับคู่มือ</a>' +
            '<button type="button" class="admin-btn admin-btn--primary" id="admin-tour-done-close">ปิด</button>' +
            '</div></div>';
        document.body.appendChild(wrap);
        document.getElementById('admin-tour-done-close').addEventListener('click', function () {
            wrap.remove();
        });
    }

    function escapeHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function renderStep() {
        var state = getState();
        if (!state || !TOURS[state.id]) return;

        var tour = TOURS[state.id];
        var step = tour.steps[state.step];
        if (!step) {
            endTour(true);
            return;
        }

        var page = currentPage();
        if (step.page !== page) {
            var target = step.go || step.page;
            window.location.href = adminUrl(target);
            return;
        }

        clearUi();

        var target = document.querySelector(step.selector);
        if (!target) {
            // Fallback: skip missing target to avoid stuck tour
            if (state.step + 1 < tour.steps.length) {
                state.step += 1;
                setState(state);
                renderStep();
            } else {
                endTour(true);
            }
            return;
        }

        target.classList.add('admin-tour-highlight');
        target.scrollIntoView({ behavior: 'smooth', block: 'center', inline: 'nearest' });

        var overlay = document.createElement('div');
        overlay.id = 'admin-tour-overlay';
        document.body.appendChild(overlay);

        var tip = document.createElement('div');
        tip.id = 'admin-tour-tip';
        tip.setAttribute('role', 'dialog');
        tip.setAttribute('aria-modal', 'true');
        tip.innerHTML =
            '<div class="admin-tour-tip__ Progressive">ขั้นตอน ' + (state.step + 1) + ' / ' + tour.steps.length + ' · ' + escapeHtml(tour.title) + '</div>' +
            '<h3 class="admin-tour-tip__title">' + escapeHtml(step.title) + '</h3>' +
            '<p class="admin-tour-tip__text">' + escapeHtml(step.text) + '</p>' +
            '<div class="admin-tour-tip__actions">' +
            '<button type="button" class="admin-btn admin-btn--outline admin-btn--sm" data-tour-act="skip">ข้ามทัวร์</button>' +
            '<div class="admin-tour-tip__nav">' +
            (state.step > 0 ? '<button type="button" class="admin-btn admin-btn--outline admin-btn--sm" data-tour-act="prev">ย้อนกลับ</button>' : '') +
            '<button type="button" class="admin-btn admin-btn--primary admin-btn--sm" data-tour-act="next">' +
            (state.step + 1 >= tour.steps.length ? 'จบขั้นตอน' : 'ถัดไป') +
            '</button>' +
            '</div></div>';
        document.body.appendChild(tip);

        // Wait for layout then position
        requestAnimationFrame(function () {
            positionTip(tip, target);
        });

        tip.addEventListener('click', function (e) {
            var btn = e.target.closest('[data-tour-act]');
            if (!btn) return;
            var act = btn.getAttribute('data-tour-act');
            if (act === 'skip') {
                endTour(false);
                return;
            }
            if (act === 'prev') {
                state.step = Math.max(0, state.step - 1);
                setState(state);
                renderStep();
                return;
            }
            if (act === 'next') {
                if (state.step + 1 >= tour.steps.length) {
                    endTour(true);
                    return;
                }
                state.step += 1;
                setState(state);
                renderStep();
            }
        });
    }

    function startTour(tourId) {
        if (!TOURS[tourId]) return;
        setState({ id: tourId, step: 0 });
        var first = TOURS[tourId].steps[0];
        if (currentPage() !== first.page) {
            window.location.href = adminUrl(first.go || first.page);
            return;
        }
        renderStep();
    }

    window.AdminTour = {
        start: startTour,
        tours: TOURS,
        stop: function () { endTour(false); }
    };

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-start-tour]').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                startTour(btn.getAttribute('data-start-tour'));
            });
        });

        var params = new URLSearchParams(window.location.search);
        var tourParam = params.get('tour');
        if (tourParam && TOURS[tourParam]) {
            var stepParam = parseInt(params.get('step') || '0', 10);
            setState({ id: tourParam, step: isNaN(stepParam) ? 0 : stepParam });
        }

        if (getState()) {
            setTimeout(renderStep, 200);
        }
    });

    window.addEventListener('resize', function () {
        var tip = document.getElementById('admin-tour-tip');
        var highlight = document.querySelector('.admin-tour-highlight');
        if (tip && highlight) positionTip(tip, highlight);
    });
})();
