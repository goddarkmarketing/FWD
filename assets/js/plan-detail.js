(function () {
  "use strict";

  const root = document.getElementById("plan-fwd");
  if (!root) return;

  initPlanNavScrollSpy();
  initPlanCalculator(root);

  function initPlanNavScrollSpy() {
    const navLinks = document.querySelectorAll(".plan-fwd-nav a");
    if (!navLinks.length) return;

    const sections = [];
    navLinks.forEach(function (link) {
      const id = link.getAttribute("href");
      if (id && id.startsWith("#")) {
        const sec = document.querySelector(id);
        if (sec) sections.push({ link: link, el: sec });
      }
    });

    if (!sections.length) return;

    function setActive(activeLink) {
      navLinks.forEach(function (link) {
        link.classList.toggle("is-active", link === activeLink);
      });
    }

    function updateActiveSection() {
      const offset = 140;
      let active = sections[0].link;

      sections.forEach(function (item) {
        if (item.el.getBoundingClientRect().top <= offset) {
          active = item.link;
        }
      });

      const nearBottom =
        window.innerHeight + window.scrollY >= document.documentElement.scrollHeight - 4;
      if (nearBottom) {
        active = sections[sections.length - 1].link;
      }

      setActive(active);
    }

    navLinks.forEach(function (link) {
      link.addEventListener("click", function () {
        setActive(link);
      });
    });

    window.addEventListener("scroll", updateActiveSection, { passive: true });
    window.addEventListener("resize", updateActiveSection, { passive: true });
    updateActiveSection();
  }

  function initPlanCalculator(planRoot) {
    let config;
    try {
      config = JSON.parse(planRoot.getAttribute("data-plan-config") || "null");
    } catch (e) {
      return;
    }

    if (!config || !Array.isArray(config.packages) || !config.packages.length) {
      return;
    }

    const packages = config.packages;
    const table = config.premium_table;
    const defaults = config.defaults || {};

    const priceEl = document.getElementById("plan-calc-price");
    const sumEl = document.getElementById("plan-calc-sum");
    const ageSelect = document.getElementById("plan-calc-age");
    const packageSelect = document.getElementById("plan-calc-package");
    const tableBody = document.querySelector("#plan-premium-table tbody");
    const sumLabel = document.getElementById("plan-table-sum-label");
    const sumRange = document.getElementById("plan-table-sum");

    let gender = defaults.gender || "male";
    let payment = defaults.payment || "yearly";
    let tableGender = "male";
    let tableSumIdx = 0;

    function ageBracketIndex(age) {
      if (age <= 25) return 0;
      if (age <= 30) return 1;
      if (age <= 35) return 2;
      if (age <= 40) return 3;
      if (age <= 45) return 4;
      return 5;
    }

    function formatMoney(n) {
      return Number(n).toLocaleString("th-TH");
    }

    function getYearlyPremium(age, pkgIndex, g) {
      const bracket = ageBracketIndex(age);
      if (table && table[g] && table[g][bracket] && table[g][bracket][pkgIndex] != null) {
        return table[g][bracket][pkgIndex];
      }
      const pkg = packages[pkgIndex];
      if (!pkg) return 0;
      const mult = [0.82, 1, 1.12, 1.35, 1.72, 2.15][bracket] || 1;
      const genderMult = g === "female" ? 0.93 : 1;
      return Math.round(pkg.premium_yearly * mult * genderMult);
    }

    function updateCalculator() {
      const age = parseInt(ageSelect.value, 10) || 30;
      const pkgIndex = parseInt(packageSelect.value, 10) || 0;
      const pkg = packages[pkgIndex];
      if (!pkg || !priceEl) return;

      const yearly = getYearlyPremium(age, pkgIndex, gender);
      const monthly = pkg.premium_monthly
        ? Math.round(
            pkg.premium_monthly *
              (yearly / (pkg.premium_yearly || yearly || 1))
          )
        : Math.ceil(yearly / 12);

      if (payment === "monthly") {
        priceEl.textContent = formatMoney(monthly) + " บาท/เดือน";
      } else {
        priceEl.textContent = formatMoney(yearly) + " บาท/ปี";
      }

      const sumLabelText = pkg.sum_label || "ทุนประกัน";
      sumEl.textContent =
        sumLabelText + " " + formatMoney(pkg.sum) + " บาท";
    }

    function renderPremiumTable() {
      if (!tableBody || !table || !table.ages) return;

      const rows = table[tableGender] || table.male;
      const sumIdx = tableSumIdx;
      const sums = table.sums || [];

      if (sumLabel && sums[sumIdx]) {
        sumLabel.textContent = formatMoney(sums[sumIdx]);
      }

      tableBody.innerHTML = "";
      table.ages.forEach(function (ageRange, i) {
        const yearly = rows[i] ? rows[i][sumIdx] : 0;
        const tr = document.createElement("tr");
        tr.innerHTML =
          "<td>" +
          ageRange +
          "</td><td>" +
          formatMoney(yearly) +
          "</td><td>" +
          formatMoney(Math.ceil(yearly / 12)) +
          "</td>";
        tableBody.appendChild(tr);
      });
    }

    document.querySelectorAll(".plan-calc__gender-btn").forEach(function (btn) {
      btn.addEventListener("click", function () {
        gender = btn.getAttribute("data-gender");
        document.querySelectorAll(".plan-calc__gender-btn").forEach(function (b) {
          b.classList.toggle("is-active", b === btn);
        });
        updateCalculator();
      });
    });

    document.querySelectorAll(".plan-calc__pay-btn").forEach(function (btn) {
      btn.addEventListener("click", function () {
        payment = btn.getAttribute("data-pay");
        document.querySelectorAll(".plan-calc__pay-btn").forEach(function (b) {
          b.classList.toggle("is-active", b === btn);
        });
        updateCalculator();
      });
    });

    if (ageSelect) ageSelect.addEventListener("change", updateCalculator);
    if (packageSelect) packageSelect.addEventListener("change", updateCalculator);

    document.querySelectorAll(".plan-table-tab").forEach(function (tab) {
      tab.addEventListener("click", function () {
        tableGender = tab.getAttribute("data-table-gender");
        document.querySelectorAll(".plan-table-tab").forEach(function (t) {
          t.classList.toggle("is-active", t === tab);
        });
        renderPremiumTable();
      });
    });

    if (sumRange) {
      sumRange.addEventListener("input", function () {
        tableSumIdx = parseInt(sumRange.value, 10) || 0;
        renderPremiumTable();
      });
    }

    updateCalculator();
    renderPremiumTable();
  }
})();
