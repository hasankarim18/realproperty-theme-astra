/**
 * Single property: gallery lightbox.
 * Opens a modal with a slider over every property image. Keyboard, swipe, and
 * focus-trap behaviour mirror filter-drawer.js. Without JS the trigger stays hidden.
 */
(function () {
  "use strict";

  var box = document.getElementById("property-lightbox");
  var trigger = document.querySelector('[data-pd-lightbox="property-lightbox"]');

  if (!box || !trigger) {
    return;
  }

  var dialog = box.querySelector(".pd-lightbox-dialog");
  var track = box.querySelector(".pd-lightbox-track");
  var slides = box.querySelectorAll(".pd-lightbox-slide");
  var counter = box.querySelector("[data-pd-lightbox-current]");
  var closeBtn = box.querySelector(".pd-lightbox-close");

  if (!dialog || !track || !slides.length) {
    return;
  }

  var total = slides.length;
  var index = 0;
  var lastFocus = null;

  function isOpen() {
    return !box.hidden;
  }

  function render() {
    track.style.transform = "translateX(-" + index * 100 + "%)";

    Array.prototype.forEach.call(slides, function (slide, i) {
      if (i === index) {
        slide.removeAttribute("aria-hidden");
        slide.setAttribute("data-active", "true");
      } else {
        slide.setAttribute("aria-hidden", "true");
        slide.removeAttribute("data-active");
      }
    });

    if (counter) {
      counter.textContent = String(index + 1);
    }
  }

  function go(next) {
    index = ((next % total) + total) % total;
    render();
  }

  function open(start) {
    if (isOpen()) {
      return;
    }
    lastFocus = document.activeElement;
    box.hidden = false;
    box.setAttribute("aria-hidden", "false");
    box.classList.add("is-open");
    document.body.classList.add("pd-lightbox-open");
    go(typeof start === "number" ? start : 0);

    if (closeBtn) {
      closeBtn.focus();
    }
  }

  function close() {
    if (!isOpen()) {
      return;
    }
    box.hidden = true;
    box.setAttribute("aria-hidden", "true");
    box.classList.remove("is-open");
    document.body.classList.remove("pd-lightbox-open");

    if (lastFocus && lastFocus.focus) {
      lastFocus.focus();
    }
  }

  /* ---------- Events ---------- */

  trigger.addEventListener("click", function () {
    open(0);
  });

  box.addEventListener("click", function (e) {
    if (e.target.closest("[data-pd-lightbox-close]")) {
      close();
      return;
    }
    if (e.target.closest("[data-pd-lightbox-prev]")) {
      go(index - 1);
      return;
    }
    if (e.target.closest("[data-pd-lightbox-next]")) {
      go(index + 1);
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

    if (e.key === "ArrowLeft") {
      e.preventDefault();
      go(index - 1);
      return;
    }

    if (e.key === "ArrowRight") {
      e.preventDefault();
      go(index + 1);
      return;
    }

    // Keep Tab focus inside the open dialog.
    if (e.key === "Tab") {
      var focusable = dialog.querySelectorAll(
        "button:not([disabled]), a[href], [tabindex]:not([tabindex='-1'])",
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

  // Swipe on touch devices: 40px either way.
  var touchStartX = null;

  box.addEventListener(
    "touchstart",
    function (e) {
      touchStartX = e.changedTouches[0].clientX;
    },
    { passive: true },
  );

  box.addEventListener(
    "touchend",
    function (e) {
      if (touchStartX === null) {
        return;
      }
      var delta = e.changedTouches[0].clientX - touchStartX;
      touchStartX = null;

      if (Math.abs(delta) < 40) {
        return;
      }
      go(delta > 0 ? index - 1 : index + 1);
    },
    { passive: true },
  );

  render();
})();
