/* Header menu toggle (mobile). */
(function () {
  "use strict";

  var toggle = document.querySelector(".nav-toggle");
  var nav = document.getElementById("primary-nav");

  if (!toggle || !nav) {
    return;
  }

  function setOpen(open) {
    nav.classList.toggle("is-open", open);
    toggle.setAttribute("aria-expanded", open ? "true" : "false");
  }

  toggle.addEventListener("click", function () {
    console.log("tooble clicked");
    setOpen(!nav.classList.contains("is-open"));
  });

  document.addEventListener("keydown", function (e) {
    if (e.key === "Escape" && nav.classList.contains("is-open")) {
      setOpen(false);
      toggle.focus();
    }
  });

  document.addEventListener("click", function (e) {
    if (
      nav.classList.contains("is-open") &&
      !e.target.closest(".site-header")
    ) {
      setOpen(false);
    }
  });

  var mq = window.matchMedia("(min-width: 901px)");
  var onChange = function (e) {
    if (e.matches) {
      setOpen(false);
    }
  };
  if (mq.addEventListener) {
    mq.addEventListener("change", onChange);
  } else if (mq.addListener) {
    mq.addListener(onChange);
  }
})();

/* Header menu toggle (mobile). */
// (function () {
//   "use strict";

//   var toggle = document.querySelector(".nav-toggle");
//   var nav = document.getElementById("primary-nav");

//   console.log(toggle);

//   if (!toggle || !nav) {
//     return;
//   }

//   console.log("header.js ready"); // remove once the menu works

//   function setOpen(open) {
//     nav.classList.toggle("is-open", open);
//     toggle.setAttribute("aria-expanded", open ? "true" : "false");
//   }

//   // Capture phase: runs before any other click handler, and keeps others from reacting to the button.
//   document.addEventListener(
//     "click",
//     function (e) {
//       if (e.target.closest(".nav-toggle")) {
//         e.stopImmediatePropagation();
//         setOpen(!nav.classList.contains("is-open"));
//         return;
//       }
//       if (
//         nav.classList.contains("is-open") &&
//         !e.target.closest(".site-header")
//       ) {
//         setOpen(false);
//       }
//     },
//     true,
//   );

//   document.addEventListener("keydown", function (e) {
//     if (e.key === "Escape" && nav.classList.contains("is-open")) {
//       setOpen(false);
//       toggle.focus();
//     }
//   });

//   var mq = window.matchMedia("(min-width: 901px)");
//   var onChange = function (e) {
//     if (e.matches) {
//       setOpen(false);
//     }
//   };
//   if (mq.addEventListener) {
//     mq.addEventListener("change", onChange);
//   } else if (mq.addListener) {
//     mq.addListener(onChange);
//   }
// })();
