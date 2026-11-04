/* =========================================================
   TFI — SMOOTH SCROLL
   Eased wheel/trackpad scrolling via Lenis (touch stays native).
   Load after /vendor/lenis.min.js.

   window.tfiScrollTo(target, offset) works with or without
   Lenis: target is a y position or an element. Element targets
   land below the sticky header unless an offset is given.
========================================================= */

(function () {
  "use strict";

  var reduceMotion = window.matchMedia &&
    window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  var lenis = null;

  function headerOffset() {
    var header = document.getElementById("site-header");
    return header ? -header.offsetHeight : 0;
  }

  if (!reduceMotion && typeof window.Lenis === "function") {
    lenis = new window.Lenis({
      lerp: 0.16,
      wheelMultiplier: 1.25,
      smoothWheel: true,
      allowNestedScroll: true,
      anchors: { offset: headerOffset() },
      autoRaf: true
    });
  }

  window.tfiLenis = lenis;

  window.tfiScrollTo = function (target, offset) {
    var isY = typeof target === "number";

    if (lenis) {
      lenis.scrollTo(target, { offset: isY ? 0 : (offset || headerOffset()) });
      return;
    }

    if (isY) {
      window.scrollTo({ top: target, behavior: reduceMotion ? "auto" : "smooth" });
    } else {
      target.scrollIntoView({ behavior: reduceMotion ? "auto" : "smooth", block: "start" });
    }
  };
})();
