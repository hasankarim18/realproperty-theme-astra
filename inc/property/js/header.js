/* Site header: mobile/tablet menu toggle and submenu accordion. */
(function () {
  "use strict";

  var toggle = document.querySelector(".nav-toggle");
  var primaryNav = document.getElementById("primary-nav");
  var mobileNav = document.getElementById("mobile-nav");

  if (!toggle || (!primaryNav && !mobileNav)) {
    return;
  }

  var mqMobileNav = window.matchMedia("(max-width: 500px)");
  var mqTablet = window.matchMedia("(max-width: 1024px)");
  var mqDesktop = window.matchMedia("(min-width: 1025px)");

  function activeNav() {
    if (mobileNav && mqMobileNav.matches) {
      return mobileNav;
    }
    return primaryNav;
  }

  function closeSubmenus(scope) {
    if (!scope) {
      return;
    }
    var openItems = scope.querySelectorAll(".menu-item-has-children.is-submenu-open");
    for (var i = 0; i < openItems.length; i++) {
      openItems[i].classList.remove("is-submenu-open");
      var btn = openItems[i].querySelector(".submenu-toggle");
      if (btn) {
        btn.setAttribute("aria-expanded", "false");
      }
    }
  }

  function setOpen(open, target) {
    if (!target) {
      return;
    }
    target.classList.toggle("is-open", open);
    toggle.setAttribute("aria-expanded", open ? "true" : "false");
    if (!open) {
      closeSubmenus(target);
      if (primaryNav && primaryNav !== target) {
        closeSubmenus(primaryNav);
      }
      if (mobileNav && mobileNav !== target) {
        closeSubmenus(mobileNav);
      }
    }
  }

  function toggleSubmenu(item, btn) {
    var willOpen = !item.classList.contains("is-submenu-open");

    var parentUl = item.parentNode;
    if (parentUl) {
      for (var i = 0; i < parentUl.children.length; i++) {
        var sibling = parentUl.children[i];
        if (sibling !== item && sibling.classList.contains("menu-item-has-children")) {
          sibling.classList.remove("is-submenu-open");
          var siblingBtn = sibling.querySelector(".submenu-toggle");
          if (siblingBtn) {
            siblingBtn.setAttribute("aria-expanded", "false");
          }
        }
      }
    }

    item.classList.toggle("is-submenu-open", willOpen);
    if (btn) {
      btn.setAttribute("aria-expanded", willOpen ? "true" : "false");
    }
  }

  function initSubmenus(scope) {
    if (!scope) {
      return;
    }

    var items = scope.querySelectorAll(".menu-item-has-children");
    for (var i = 0; i < items.length; i++) {
      (function (item, navEl) {
        var link = null;
        var submenu = null;

        for (var j = 0; j < item.children.length; j++) {
          var child = item.children[j];
          if (child.tagName === "A") {
            link = child;
          } else if (child.tagName === "UL" && child.classList.contains("sub-menu")) {
            submenu = child;
          }
        }

        if (!link || !submenu || item.querySelector(".submenu-toggle")) {
          return;
        }

        if (!submenu.id) {
          submenu.id = "submenu-" + i + "-" + Math.random().toString(36).slice(2, 7);
        }

        var btn = document.createElement("button");
        btn.type = "button";
        btn.className = "submenu-toggle";
        btn.setAttribute("aria-expanded", "false");
        btn.setAttribute("aria-controls", submenu.id);
        btn.setAttribute(
          "aria-label",
          "Toggle submenu" + (link.textContent ? " for " + link.textContent.trim() : "")
        );

        btn.addEventListener("click", function (e) {
          e.stopPropagation();
          toggleSubmenu(item, btn);
        });

        link.addEventListener("click", function (e) {
          if (!mqTablet.matches || !navEl.classList.contains("is-open")) {
            return;
          }
          e.preventDefault();
          e.stopPropagation();
          toggleSubmenu(item, btn);
        });

        item.insertBefore(btn, submenu);
      })(items[i], scope);
    }
  }

  function addNavClickClose(navEl) {
    if (!navEl) {
      return;
    }
    navEl.addEventListener("click", function (e) {
      if (!mqTablet.matches || !navEl.classList.contains("is-open")) {
        return;
      }
      var link = e.target.closest("a");
      if (link && !link.parentNode.classList.contains("menu-item-has-children")) {
        setOpen(false, navEl);
      }
    });
  }

  initSubmenus(primaryNav);
  initSubmenus(mobileNav);
  addNavClickClose(primaryNav);
  addNavClickClose(mobileNav);

  var initialNav = activeNav();
  if (initialNav) {
    toggle.setAttribute("aria-controls", initialNav.id);
  }

  toggle.addEventListener("click", function () {
    var target = activeNav();
    if (!target) {
      return;
    }
    var isOpen = target.classList.contains("is-open");
    setOpen(!isOpen, target);
    toggle.setAttribute("aria-controls", target.id);
  });

  function closeAllOpen() {
    if (primaryNav && primaryNav.classList.contains("is-open")) {
      setOpen(false, primaryNav);
    }
    if (mobileNav && mobileNav.classList.contains("is-open")) {
      setOpen(false, mobileNav);
    }
  }

  document.addEventListener("keydown", function (e) {
    if (e.key === "Escape") {
      closeAllOpen();
      toggle.focus();
    }
  });

  document.addEventListener("click", function (e) {
    var anyOpen =
      (primaryNav && primaryNav.classList.contains("is-open")) ||
      (mobileNav && mobileNav.classList.contains("is-open"));
    if (anyOpen && !e.target.closest(".site-header")) {
      closeAllOpen();
    }
  });

  var onChange = function () {
    closeAllOpen();
  };

  if (mqDesktop.addEventListener) {
    mqDesktop.addEventListener("change", onChange);
  } else if (mqDesktop.addListener) {
    mqDesktop.addListener(onChange);
  }

  if (mqMobileNav.addEventListener) {
    mqMobileNav.addEventListener("change", onChange);
  } else if (mqMobileNav.addListener) {
    mqMobileNav.addListener(onChange);
  }
})();
