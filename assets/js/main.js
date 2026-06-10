(function () {
  "use strict";

  const navToggle = document.getElementById("nav-toggle");
  const siteNav = document.getElementById("site-nav");
  const header = document.getElementById("site-header");

  if (navToggle && siteNav) {
    navToggle.addEventListener("click", function () {
      const open = siteNav.classList.toggle("is-open");
      navToggle.setAttribute("aria-expanded", open ? "true" : "false");
      document.body.style.overflow = open ? "hidden" : "";
    });
  }

  document.querySelectorAll(".site-nav__trigger").forEach(function (btn) {
    btn.addEventListener("click", function (e) {
      if (window.innerWidth <= 768) {
        e.preventDefault();
        const item = btn.closest(".site-nav__item--has-sub");
        if (item) {
          item.classList.toggle("is-open");
          btn.setAttribute(
            "aria-expanded",
            item.classList.contains("is-open") ? "true" : "false"
          );
        }
      }
    });
  });

  document.querySelectorAll(".tab-btn").forEach(function (btn) {
    btn.addEventListener("click", function () {
      const target = btn.getAttribute("data-tab");
      const parent = btn.closest(".product-detail__tabs")?.parentElement;
      if (!parent || !target) return;

      parent.querySelectorAll(".tab-btn").forEach(function (b) {
        b.classList.remove("is-active");
      });
      parent.querySelectorAll(".tab-panel").forEach(function (p) {
        p.classList.remove("is-active");
      });

      btn.classList.add("is-active");
      const panel = parent.querySelector('[data-panel="' + target + '"]');
      if (panel) panel.classList.add("is-active");
    });
  });

  const reveals = document.querySelectorAll(".reveal");
  if (reveals.length && "IntersectionObserver" in window) {
    const observer = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            entry.target.classList.add("is-visible");
            observer.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.12, rootMargin: "0px 0px -40px 0px" }
    );
    reveals.forEach(function (el) {
      observer.observe(el);
    });
  } else {
    reveals.forEach(function (el) {
      el.classList.add("is-visible");
    });
  }

  const cookieBanner = document.getElementById("cookie-banner");
  const cookieAccept = document.getElementById("cookie-accept");
  if (cookieBanner && cookieAccept) {
    if (localStorage.getItem("fwd-cookie-ok")) {
      cookieBanner.classList.add("is-hidden");
    } else {
      requestAnimationFrame(function () {
        cookieBanner.classList.add("is-visible");
      });
    }
    cookieAccept.addEventListener("click", function () {
      localStorage.setItem("fwd-cookie-ok", "1");
      cookieBanner.classList.remove("is-visible");
      setTimeout(function () {
        cookieBanner.classList.add("is-hidden");
      }, 300);
    });
  }

  const contactForm = document.getElementById("contact-form");
  if (contactForm) {
    contactForm.addEventListener("submit", function (e) {
      e.preventDefault();
      const msg = document.getElementById("form-success");
      if (msg) {
        msg.hidden = false;
        contactForm.reset();
        msg.scrollIntoView({ behavior: "smooth", block: "center" });
      }
    });
  }

  const agentDob = document.getElementById("agent_dob");
  const agentAge = document.getElementById("agent_age");
  if (agentDob && agentAge) {
    function updateAgentAge() {
      const value = agentDob.value;
      if (!value) {
        agentAge.value = "";
        return;
      }
      const birth = new Date(value + "T00:00:00");
      const today = new Date();
      let age = today.getFullYear() - birth.getFullYear();
      const monthDiff = today.getMonth() - birth.getMonth();
      if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
        age -= 1;
      }
      agentAge.value = age >= 0 ? String(age) : "";
    }
    agentDob.addEventListener("change", updateAgentAge);
    agentDob.addEventListener("input", updateAgentAge);
  }

  const agentForm = document.getElementById("agent-apply-form");
  if (agentForm) {
    agentForm.addEventListener("submit", function (e) {
      e.preventDefault();
      const msg = document.getElementById("agent-form-success");
      if (msg) {
        msg.hidden = false;
        agentForm.reset();
        if (agentAge) {
          agentAge.value = "";
        }
        msg.scrollIntoView({ behavior: "smooth", block: "center" });
      }
    });
  }

  const licenseOpen = document.getElementById("agent-license-open");
  const licenseModal = document.getElementById("agent-license-modal");
  if (licenseOpen && licenseModal) {
    function openLicenseModal() {
      licenseModal.classList.add("is-open");
      licenseModal.setAttribute("aria-hidden", "false");
      document.body.style.overflow = "hidden";
    }
    function closeLicenseModal() {
      licenseModal.classList.remove("is-open");
      licenseModal.setAttribute("aria-hidden", "true");
      document.body.style.overflow = "";
    }
    licenseOpen.addEventListener("click", openLicenseModal);
    licenseModal.querySelectorAll("[data-license-close]").forEach(function (el) {
      el.addEventListener("click", closeLicenseModal);
    });
    document.addEventListener("keydown", function (e) {
      if (e.key === "Escape" && licenseModal.classList.contains("is-open")) {
        closeLicenseModal();
      }
    });
  }

  function initProductSlider(root) {
    if (!root || root.classList.contains("is-empty")) return null;

    const viewport = root.querySelector(".product-slider__viewport");
    const track =
      root.querySelector(".product-slider__track") ||
      root.querySelector(".plan-grid--carousel");
    let items = track ? Array.from(track.querySelectorAll(".product-slider__item")) : [];
    const prevBtn = root.querySelector(".product-slider__arrow--prev");
    const nextBtn = root.querySelector(".product-slider__arrow--next");
    const dotsEl =
      document.getElementById("plan-grid-dots") ||
      root.parentElement?.querySelector(":scope > .product-slider__dots") ||
      root.querySelector(".product-slider__dots");

    if (!viewport || !track || items.length === 0) return null;

    let page = 0;

    function getPerView() {
      if (root.classList.contains("product-slider--promo")) {
        if (window.matchMedia("(max-width: 899px)").matches) return 1;
        return parseInt(root.getAttribute("data-per-view") || "3", 10);
      }
      if (window.matchMedia("(max-width: 639px)").matches) return 1;
      if (window.matchMedia("(max-width: 899px)").matches) return 2;
      return parseInt(root.getAttribute("data-per-view") || "3", 10);
    }

    function getVisibleItems() {
      return items.filter(function (item) {
        if (item.classList.contains("is-filtered-out")) return false;
        const card = item.querySelector(".plan-card");
        return !card || !card.classList.contains("is-filtered-out");
      });
    }

    function getPageCount() {
      const visible = getVisibleItems().length;
      if (visible === 0) return 1;
      return Math.max(1, Math.ceil(visible / getPerView()));
    }

    function getGap() {
      if (
        root.classList.contains("product-slider--promo") &&
        window.matchMedia("(max-width: 899px)").matches
      ) {
        return 0;
      }
      const gap = parseFloat(getComputedStyle(track).columnGap || getComputedStyle(track).gap);
      return Number.isFinite(gap) ? gap : 24;
    }

    function getViewportInnerWidth() {
      return Math.round(viewport.getBoundingClientRect().width);
    }

    function getItemWidth() {
      const pv = getPerView();
      const gap = getGap();
      return (getViewportInnerWidth() - gap * (pv - 1)) / pv;
    }

    function getPageOffset(targetPage) {
      const visible = getVisibleItems();
      if (visible.length === 0) return 0;
      const pv = getPerView();
      const startIndex = Math.min(targetPage * pv, visible.length - 1);
      return visible[startIndex].offsetLeft;
    }

    function layoutItems() {
      const itemWidth = getItemWidth();
      items.forEach(function (item) {
        if (item.classList.contains("is-filtered-out")) {
          item.style.flex = "0 0 0";
          item.style.width = "0";
          item.style.maxWidth = "0";
          return;
        }
        item.style.flex = "0 0 " + itemWidth + "px";
        item.style.width = itemWidth + "px";
        item.style.maxWidth = itemWidth + "px";
      });
    }

    function renderDots() {
      if (!dotsEl) return;
      const count = getPageCount();
      dotsEl.innerHTML = "";
      dotsEl.classList.toggle("product-slider__dots--compact", count > 5);

      if (count > 5) {
        const progress = document.createElement("div");
        progress.className = "product-slider__progress";
        progress.setAttribute("role", "progressbar");
        progress.setAttribute("aria-valuemin", "1");
        progress.setAttribute("aria-valuemax", String(count));
        progress.setAttribute("aria-valuenow", String(page + 1));

        const fill = document.createElement("div");
        fill.className = "product-slider__progress-fill";
        fill.style.width = ((page + 1) / count) * 100 + "%";
        progress.appendChild(fill);

        const label = document.createElement("span");
        label.className = "product-slider__pager";
        label.textContent = page + 1 + " / " + count;

        dotsEl.appendChild(progress);
        dotsEl.appendChild(label);
        return;
      }

      for (let i = 0; i < count; i++) {
        const dot = document.createElement("button");
        dot.type = "button";
        dot.className = "product-slider__dot" + (i === page ? " is-active" : "");
        dot.setAttribute("aria-label", "หน้า " + (i + 1));
        dot.setAttribute("role", "tab");
        dot.setAttribute("aria-selected", i === page ? "true" : "false");
        dot.addEventListener("click", function () {
          page = i;
          update();
        });
        dotsEl.appendChild(dot);
      }
    }

    function update() {
      layoutItems();
      const maxPage = getPageCount() - 1;
      if (getVisibleItems().length === 0) {
        page = 0;
      } else {
        page = Math.max(0, Math.min(page, maxPage));
      }
      track.style.transform = "translateX(-" + getPageOffset(page) + "px)";
      if (prevBtn) prevBtn.disabled = page <= 0;
      if (nextBtn) nextBtn.disabled = page >= maxPage;
      renderDots();
    }

    if (prevBtn) {
      prevBtn.addEventListener("click", function () {
        page -= 1;
        update();
      });
    }
    if (nextBtn) {
      nextBtn.addEventListener("click", function () {
        page += 1;
        update();
      });
    }

    let touchStartX = 0;
    root.addEventListener(
      "touchstart",
      function (e) {
        touchStartX = e.changedTouches[0].screenX;
      },
      { passive: true }
    );
    root.addEventListener(
      "touchend",
      function (e) {
        const diff = e.changedTouches[0].screenX - touchStartX;
        if (Math.abs(diff) < 50) return;
        if (diff < 0) page += 1;
        else page -= 1;
        update();
      },
      { passive: true }
    );

    window.addEventListener("resize", update);
    update();

    return {
      refresh: update,
      reset: function () {
        page = 0;
        update();
      },
    };
  }

  const planSlider = initProductSlider(document.getElementById("plan-grid-slider"));

  const planGridWrap = document.getElementById("plan-grid-wrap");
  if (planGridWrap) {
    const planCards = Array.from(document.querySelectorAll("#plan-grid .plan-card"));
    const emptyMsg = document.getElementById("plan-empty");
    const searchInput = document.getElementById("product-search");
    const filterBtns = document.querySelectorAll("#product-filters .product-filters__btn");
    const promoPanel = document.getElementById("plans-promo-panel");
    const promoIcon = document.getElementById("plans-promo-icon");
    const promoEyebrow = document.getElementById("plans-promo-eyebrow");
    const promoTitle = document.getElementById("plans-promo-title");
    const promoDesc = document.getElementById("plans-promo-desc");
    let activeFilter = "all";

    function isAllFilter() {
      return !activeFilter || activeFilter === "all";
    }

    function getActiveFilterBtn() {
      return (
        Array.from(filterBtns).find(function (b) {
          return (b.getAttribute("data-filter") || "") === activeFilter;
        }) || filterBtns[0]
      );
    }

    function updatePromoPanel() {
      const btn = getActiveFilterBtn();
      if (!btn) return;

      const title = btn.getAttribute("data-panel-title") || "";
      const desc = btn.getAttribute("data-panel-desc") || "";
      const label = btn.getAttribute("data-panel-label") || "";
      const iconEl = btn.querySelector(".product-filters__icon");

      if (promoTitle) promoTitle.textContent = title;
      if (promoDesc) promoDesc.textContent = desc;
      if (promoEyebrow) promoEyebrow.textContent = label;
      if (promoPanel) promoPanel.setAttribute("aria-label", title);
      if (promoIcon && iconEl) promoIcon.innerHTML = iconEl.innerHTML;
    }

    function applyPlanFilters() {
      const query = (searchInput ? searchInput.value : "").trim().toLowerCase();

      planCards.forEach(function (card) {
        const category = card.getAttribute("data-category") || "";
        const searchText = card.getAttribute("data-search") || "";
        const matchCategory = isAllFilter() || category === activeFilter;
        const matchSearch = !query || searchText.indexOf(query) !== -1;
        card.classList.toggle("is-filtered-out", !(matchCategory && matchSearch));
      });

      document.querySelectorAll("#plan-grid .product-slider__item").forEach(function (item) {
        const card = item.querySelector(".plan-card");
        const hidden = card && card.classList.contains("is-filtered-out");
        item.classList.toggle("is-filtered-out", hidden);
      });

      const carouselVisible = Array.from(
        document.querySelectorAll("#plan-grid .plan-card")
      ).filter(function (c) {
        return !c.classList.contains("is-filtered-out");
      });
      if (emptyMsg) emptyMsg.hidden = carouselVisible.length > 0;
      if (planSlider) planSlider.reset();
      updatePromoPanel();
    }

    function setFilterAll() {
      activeFilter = "all";
      filterBtns.forEach(function (b) {
        const on = (b.getAttribute("data-filter") || "") === "all";
        b.classList.toggle("is-active", on);
        b.setAttribute("aria-pressed", on ? "true" : "false");
      });
      applyPlanFilters();
    }

    filterBtns.forEach(function (btn) {
      btn.addEventListener("click", function () {
        const id = btn.getAttribute("data-filter") || "all";
        if (id === "all") {
          setFilterAll();
          return;
        }
        if (btn.classList.contains("is-active")) {
          setFilterAll();
          return;
        }
        activeFilter = id;
        filterBtns.forEach(function (b) {
          const on = b === btn;
          b.classList.toggle("is-active", on);
          b.setAttribute("aria-pressed", on ? "true" : "false");
        });
        applyPlanFilters();
      });
    });

    if (searchInput) {
      searchInput.addEventListener("input", applyPlanFilters);
    }

    applyPlanFilters();
  }

  let lastScroll = 0;
  window.addEventListener(
    "scroll",
    function () {
      if (!header) return;
      const y = window.scrollY;
      if (y > 80 && y > lastScroll) {
        header.style.transform = "translateY(-100%)";
      } else {
        header.style.transform = "";
      }
      lastScroll = y;
    },
    { passive: true }
  );

  const contactFab = document.getElementById("contact-fab");
  const contactFabToggle = document.getElementById("contact-fab-toggle");
  const contactFabBackdrop = document.getElementById("contact-fab-backdrop");

  function setContactFabOpen(open) {
    if (!contactFab || !contactFabToggle) return;
    contactFab.classList.toggle("is-open", open);
    contactFabToggle.setAttribute("aria-expanded", open ? "true" : "false");
    contactFabToggle.setAttribute("aria-label", open ? "ปิดช่องทางติดต่อ" : "เปิดช่องทางติดต่อ");
    document.body.classList.toggle("contact-fab-open", open);
    if (contactFabBackdrop) {
      contactFabBackdrop.tabIndex = open ? 0 : -1;
      contactFabBackdrop.setAttribute("aria-hidden", open ? "false" : "true");
    }
  }

  if (contactFabToggle) {
    contactFabToggle.addEventListener("click", function () {
      setContactFabOpen(!contactFab.classList.contains("is-open"));
    });
  }

  if (contactFabBackdrop) {
    contactFabBackdrop.addEventListener("click", function () {
      setContactFabOpen(false);
    });
  }

  document.addEventListener("keydown", function (e) {
    if (e.key === "Escape" && contactFab && contactFab.classList.contains("is-open")) {
      setContactFabOpen(false);
      contactFabToggle?.focus();
    }
  });
})();
