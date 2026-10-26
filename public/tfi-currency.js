(function () {
  "use strict";

  var stored = "";
  try { stored = localStorage.getItem("tfi-currency") || ""; } catch (e) {}
  var currency = stored === "usd" ? "usd" : "inr";
  window.TFI_CURRENCY = currency;

  function format(amount) {
    var n = Number(amount) || 0;
    if (currency === "usd") {
      return "$" + n.toLocaleString("en-US", { minimumFractionDigits: n % 1 ? 2 : 0, maximumFractionDigits: 2 });
    }
    return "₹" + Math.round(n).toLocaleString("en-IN");
  }

  window.TFI = window.TFI || {};
  window.TFI.currency = currency;
  window.TFI.money = format;
  window.TFI.pack = function () {
    var packs = window.TFI_PLAN_PRICES || {};
    return packs[currency] || packs.inr || null;
  };

  function switchTo(next) {
    try { localStorage.setItem("tfi-currency", next); } catch (e) {}
    window.location.reload();
  }

  function mountToggle() {
    if (document.querySelector(".tfi-currency-toggle")) return;
    var host = document.querySelector(".site-header .navrow") || document.querySelector(".site-header");
    if (!host) return;

    var wrap = document.createElement("div");
    wrap.className = "tfi-currency-toggle";
    wrap.setAttribute("role", "group");
    wrap.setAttribute("aria-label", "Currency");
    wrap.innerHTML =
      '<button type="button" data-cur="inr"' + (currency === "inr" ? ' class="is-on"' : "") + '>INR</button>' +
      '<button type="button" data-cur="usd"' + (currency === "usd" ? ' class="is-on"' : "") + '>USD</button>';

    wrap.addEventListener("click", function (event) {
      var btn = event.target.closest("button[data-cur]");
      if (!btn || btn.getAttribute("data-cur") === currency) return;
      switchTo(btn.getAttribute("data-cur"));
    });

    host.appendChild(wrap);

    if (document.getElementById("tfi-currency-style")) return;
    var css = document.createElement("style");
    css.id = "tfi-currency-style";
    css.textContent =
      ".tfi-currency-toggle{display:inline-flex;margin-left:.6rem;border:1px solid rgba(212,168,58,.45);border-radius:999px;overflow:hidden}" +
      ".tfi-currency-toggle button{background:transparent;color:inherit;border:0;padding:.35rem .7rem;font:inherit;font-size:.72rem;font-weight:700;letter-spacing:.08em;cursor:pointer}" +
      ".tfi-currency-toggle button.is-on{background:linear-gradient(135deg,#f7df9a,#d4a83a);color:#17130a}";
    document.head.appendChild(css);
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", mountToggle);
  } else {
    mountToggle();
  }
})();
