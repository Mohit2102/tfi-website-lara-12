(function () {
  "use strict";

  var style = document.createElement("style");
  style.textContent =
    "html,body{-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none;-webkit-touch-callout:none}" +
    "input,textarea,select,[contenteditable],[contenteditable] *{-webkit-user-select:text;-moz-user-select:text;-ms-user-select:text;user-select:text}" +
    "img,svg,video,picture{-webkit-user-drag:none;user-drag:none}" +
    "@media print{body{display:none!important}}";
  (document.head || document.documentElement).appendChild(style);

  function isEditable(el) {
    if (!el || el.nodeType !== 1) return false;
    if (el.isContentEditable) return true;
    var tag = el.tagName;
    return tag === "INPUT" || tag === "TEXTAREA" || tag === "SELECT";
  }

  function block(e) {
    e.preventDefault();
    e.stopPropagation();
    return false;
  }

  document.addEventListener("contextmenu", function (e) {
    if (!isEditable(e.target)) block(e);
  }, true);

  ["copy", "cut", "selectstart"].forEach(function (type) {
    document.addEventListener(type, function (e) {
      if (!isEditable(e.target)) block(e);
    }, true);
  });

  document.addEventListener("dragstart", function (e) {
    if (!isEditable(e.target)) block(e);
  }, true);

  document.addEventListener("keydown", function (e) {
    var key = (e.key || "").toLowerCase();
    var mod = e.ctrlKey || e.metaKey;

    if (key === "f12") return block(e);

    // DevTools: Ctrl+Shift+I/J/C/K (Windows/Linux), Cmd+Option+I/J/C (macOS)
    if (mod && (e.shiftKey || e.altKey) && ["i", "j", "c", "k"].indexOf(key) !== -1) return block(e);

    // View source, save page, print
    if (mod && ["u", "s", "p"].indexOf(key) !== -1) return block(e);

    // Select all / copy / cut outside form fields
    if (mod && ["a", "c", "x"].indexOf(key) !== -1 && !isEditable(e.target)) return block(e);
  }, true);

  // Pauses page scripts whenever DevTools is open; a no-op for normal visitors.
  setInterval(function () {
    var start = Date.now();
    (function () { debugger; })();
    if (Date.now() - start > 100) {
      document.documentElement.innerHTML = "";
      window.location.replace("about:blank");
    }
  }, 1000);
})();
