/**
 * Mobile filter drawer: open/close, focus handling, and a live result count on the button.
 * Independent of archive.js (the drawer still works if filtering falls back to page loads).
 */
(function () {
  "use strict";

  var btn = document.querySelector(".mobile-filter-button");
  var sidebar = document.getElementById("filter-sidebar");
  var overlay = document.querySelector(".filter-overlay");

  if (!btn || !sidebar || !overlay) {
    return;
  }

  var mq = window.matchMedia("(max-width: 720px)");
  var lastFocus = null;

  function isOpen() {
    return sidebar.classList.contains("is-open");
  }

  function open() {
    if (isOpen()) {
      return;
    }
    lastFocus = document.activeElement;
    sidebar.classList.add("is-open");
    overlay.classList.add("is-open");
    document.body.classList.add("filters-open");
    btn.setAttribute("aria-expanded", "true");
    sidebar.setAttribute("role", "dialog");
    sidebar.setAttribute("aria-modal", "true");

    var closeBtn = sidebar.querySelector(".drawer-close");
    if (closeBtn) {
      closeBtn.focus();
    }
  }

  function close() {
    if (!isOpen()) {
      return;
    }
    sidebar.classList.remove("is-open");
    overlay.classList.remove("is-open");
    document.body.classList.remove("filters-open");
    btn.setAttribute("aria-expanded", "false");
    sidebar.removeAttribute("role");
    sidebar.removeAttribute("aria-modal");

    if (lastFocus && lastFocus.focus) {
      lastFocus.focus();
    }
  }

  btn.addEventListener("click", open);
  overlay.addEventListener("click", close);

  sidebar.addEventListener("click", function (e) {
    if (e.target.closest(".drawer-close, .drawer-done")) {
      close();
    }
  });

  document.addEventListener("keydown", function (e) {
    if (!isOpen()) {
      return;
    }

    if (e.key === "Escape") {
      close();
      return;
    }

    // Keep Tab focus inside the open drawer.
    if (e.key === "Tab") {
      var focusable = sidebar.querySelectorAll(
        "a[href], button:not([disabled]), input:not([disabled]), select:not([disabled])",
      );
      var visible = Array.prototype.filter.call(focusable, function (el) {
        return el.offsetParent !== null;
      });
      if (!visible.length) {
        return;
      }
      var first = visible[0];
      var last = visible[visible.length - 1];

      if (e.shiftKey && document.activeElement === first) {
        e.preventDefault();
        last.focus();
      } else if (!e.shiftKey && document.activeElement === last) {
        e.preventDefault();
        first.focus();
      }
    }
  });

  // Resizing to desktop closes the drawer and unlocks scrolling.
  function onBreakpoint() {
    if (!mq.matches) {
      close();
    }
  }
  if (mq.addEventListener) {
    mq.addEventListener("change", onBreakpoint);
  } else if (mq.addListener) {
    mq.addListener(onBreakpoint);
  }

  // "Show N properties" label follows the results count.
  var done = sidebar.querySelector(".drawer-done");
  var count = document.getElementById("results-count");

  function updateLabel() {
    if (!done || !count) {
      return;
    }
    var strongs = count.querySelectorAll("strong");
    var total = strongs.length > 1 ? strongs[1].textContent.trim() : "0";

    if (total === "0") {
      done.textContent = "No properties found";
    } else if (total === "1") {
      done.textContent = "Show 1 property";
    } else {
      done.textContent = "Show " + total + " properties";
    }
  }

  if (count && window.MutationObserver) {
    new MutationObserver(updateLabel).observe(count, {
      childList: true,
      subtree: true,
      characterData: true,
    });
  }
  updateLabel();
})();
