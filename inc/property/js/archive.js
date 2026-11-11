/**
 * Property archive: AJAX filtering.
 * The URL is the only state. We fetch the same archive URL, swap the results,
 * and push the URL to history. Any failure falls back to a normal page load.
 */
(function () {
  "use strict";

  var form = document.getElementById("sidebar-form");
  var area = document.getElementById("results-area");

  if (
    !form ||
    !area ||
    !window.fetch ||
    !window.DOMParser ||
    !window.URLSearchParams ||
    !window.AbortController ||
    !history.pushState
  ) {
    return;
  }

  var archiveBase = form.getAttribute("action");
  var controller = null;
  var timer = null;

  /* ---------- URL helpers ---------- */

  // Group params by name. Handles both status[] and status[0] style keys.
  function normalize(searchParams) {
    var out = {};
    searchParams.forEach(function (value, key) {
      var name = key.replace(/\[\d*\]$/, "");
      (out[name] = out[name] || []).push(value);
    });
    return out;
  }

  // Build the archive URL from the sidebar form (and the sort select linked to it).
  function buildUrl() {
    var params = new URLSearchParams();

    new FormData(form).forEach(function (value, key) {
      if (typeof value !== "string" || key === "post_type") {
        return;
      }
      value = value.trim();
      if (value !== "") {
        params.append(key, value);
      }
    });

    if (params.has("s") && archiveBase.indexOf("post_type=") === -1) {
      params.set("post_type", "property");
    }

    var qs = params.toString();
    if (!qs) {
      return archiveBase;
    }

    return archiveBase + (archiveBase.indexOf("?") === -1 ? "?" : "&") + qs;
  }

  /* ---------- Sync the UI from a URL ---------- */

  function syncFromUrl(url) {
    var values = normalize(new URL(url, location.href).searchParams);

    Array.prototype.forEach.call(form.elements, function (el) {
      var name = (el.name || "").replace(/\[\]$/, "");
      if (
        !name ||
        el.type === "hidden" ||
        el.type === "submit" ||
        el.type === "button"
      ) {
        return;
      }

      var list = values[name] || [];

      if (el.type === "checkbox") {
        el.checked = list.indexOf(el.value) !== -1;
      } else if (el.type === "radio") {
        el.checked = (list[0] || "") === el.value;
      } else {
        el.value = list[0] || "";
      }
    });

    // Keyword: hidden copy in the sidebar form + the hero input.
    var keyword = (values.s && values.s[0]) || "";
    var hiddenS = form.querySelector('input[type="hidden"][name="s"]');
    var hiddenType = form.querySelector(
      'input[type="hidden"][name="post_type"]',
    );

    if (keyword) {
      if (!hiddenS) {
        hiddenS = document.createElement("input");
        hiddenS.type = "hidden";
        hiddenS.name = "s";
        form.insertBefore(hiddenS, form.firstChild);
      }
      hiddenS.value = keyword;

      if (!hiddenType) {
        hiddenType = document.createElement("input");
        hiddenType.type = "hidden";
        hiddenType.name = "post_type";
        hiddenType.value = "property";
        form.insertBefore(hiddenType, form.firstChild);
      }
    } else {
      if (hiddenS) {
        hiddenS.remove();
      }
      if (hiddenType) {
        hiddenType.remove();
      }
    }

    var heroInput = document.querySelector('.search-bar input[name="s"]');
    if (heroInput) {
      heroInput.value = keyword;
    }
  }

  /* ---------- Load results ---------- */

  function scrollToResults() {
    var target = document.querySelector(".archive-heading");
    if (!target) {
      return;
    }
    var reduce =
      window.matchMedia &&
      window.matchMedia("(prefers-reduced-motion: reduce)").matches;
    target.scrollIntoView({
      behavior: reduce ? "auto" : "smooth",
      block: "start",
    });
  }

  function load(url, opts) {
    if (controller) {
      controller.abort();
    }

    var current = new AbortController();
    controller = current;

    area.classList.add("is-loading");
    area.setAttribute("aria-busy", "true");

    if (opts.sync) {
      syncFromUrl(url);
    }

    fetch(url, {
      signal: current.signal,
      credentials: "same-origin",
      headers: { "X-Requested-With": "fetch" },
    })
      .then(function (res) {
        if (!res.ok) {
          throw new Error("Bad response");
        }
        return res.text().then(function (html) {
          return { html: html, finalUrl: res.url || url };
        });
      })
      .then(function (result) {
        var doc = new DOMParser().parseFromString(result.html, "text/html");
        var newArea = doc.getElementById("results-area");
        var newCount = doc.getElementById("results-count");
        var count = document.getElementById("results-count");

        if (!newArea || !newCount || !count) {
          throw new Error("Unexpected markup");
        }

        area.innerHTML = newArea.innerHTML;
        count.innerHTML = newCount.innerHTML;

        var crumbs = document.querySelector(".breadcrumbs");
        var newCrumbs = doc.querySelector(".breadcrumbs");
        if (crumbs && newCrumbs) {
          crumbs.innerHTML = newCrumbs.innerHTML;
        }

        if (doc.title) {
          document.title = doc.title;
        }

        // Only keep the final URL if it is still on our site.
        var finalUrl = result.finalUrl;
        try {
          if (new URL(finalUrl, location.href).origin !== location.origin) {
            finalUrl = url;
          }
        } catch (e) {
          finalUrl = url;
        }

        if (opts.push) {
          history.pushState({ pd: true }, "", finalUrl);
        }

        if (opts.scroll) {
          scrollToResults();
        }
      })
      .catch(function (err) {
        if (err && err.name === "AbortError") {
          return; // A newer request replaced this one.
        }
        window.location.href = url; // Safe fallback: normal navigation.
      })
      .then(function () {
        if (controller === current) {
          area.classList.remove("is-loading");
          area.removeAttribute("aria-busy");
        }
      });
  }

  // Navigate to a URL via AJAX, skipping it if it is already the current one.
  function go(url, opts) {
    var target = new URL(url, location.href).href;
    if (target === location.href) {
      return;
    }
    opts.push = true;
    load(target, opts);
  }

  /* ---------- Events ---------- */

  // Checkboxes, radios, selects (including the sort select linked by form="sidebar-form").
  document.addEventListener("change", function (e) {
    var el = e.target;
    if (!el.form || el.form !== form) {
      return;
    }
    clearTimeout(timer);
    go(buildUrl(), {});
  });

  // Number inputs: debounced.
  document.addEventListener("input", function (e) {
    var el = e.target;
    if (!el.form || el.form !== form || el.type !== "number") {
      return;
    }
    clearTimeout(timer);
    timer = setTimeout(function () {
      go(buildUrl(), {});
    }, 500);
  });

  // "Show properties" button / Enter key.
  form.addEventListener("submit", function (e) {
    e.preventDefault();
    clearTimeout(timer);
    go(buildUrl(), {});
  });

  // Pagination, chips, Clear all, empty-state link.
  document.addEventListener("click", function (e) {
    if (
      e.defaultPrevented ||
      e.button !== 0 ||
      e.metaKey ||
      e.ctrlKey ||
      e.shiftKey ||
      e.altKey
    ) {
      return;
    }

    var link = e.target.closest(
      ".pagination a, .active-filters a, .clear-filters, .remove-all, .no-results a",
    );
    if (!link || !link.href) {
      return;
    }

    var url = new URL(link.href, location.href);
    if (url.origin !== location.origin) {
      return;
    }

    e.preventDefault();
    clearTimeout(timer);
    go(url.href, { sync: true, scroll: !!link.closest(".pagination") });
  });

  // Back / forward: re-sync the sidebar from the URL and reload without pushing.
  window.addEventListener("popstate", function () {
    clearTimeout(timer);
    load(location.href, { push: false, sync: true });
  });
})();

console.log({
  form: !!document.getElementById("sidebar-form"),
  area: !!document.getElementById("results-area"),
  count: !!document.getElementById("results-count"),
  fetch: !!window.fetch,
  DOMParser: !!window.DOMParser,
  URLSearchParams: !!window.URLSearchParams,
  AbortController: !!window.AbortController,
  pushState: !!history.pushState,
  jsClass: document.documentElement.classList.contains("js"),
});

console.log({
  resultsAreaClass: !!document.querySelector(".results-area"),
  grid: !!document.querySelector(".property-grid"),
  countP: document.querySelector(".archive-heading p")
    ? document.querySelector(".archive-heading p").outerHTML.slice(0, 120)
    : null,
});
