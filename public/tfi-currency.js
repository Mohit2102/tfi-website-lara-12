(function () {
  "use strict";

  var STORE_KEY = "tfi-currency";
  var COUNTRY_KEY = "tfi-country";

  function read(key) {
    try { return localStorage.getItem(key) || ""; } catch (e) { return ""; }
  }

  function write(key, value) {
    try { localStorage.setItem(key, value); } catch (e) {}
  }

  function usdPricesReady() {
    var usd = (window.TFI_PLAN_PRICES || {}).usd;
    if (!usd) return false;

    function anyPositive(value) {
      if (typeof value === "number") return value > 0;
      if (!value || typeof value !== "object") return false;
      for (var key in value) {
        if (Object.prototype.hasOwnProperty.call(value, key) && anyPositive(value[key])) return true;
      }
      return false;
    }

    return anyPositive(usd);
  }

  function indiaFromDevice() {
    var tz = "";
    try { tz = Intl.DateTimeFormat().resolvedOptions().timeZone || ""; } catch (e) {}
    if (tz === "Asia/Kolkata" || tz === "Asia/Calcutta") return true;

    var lang = (navigator.language || navigator.userLanguage || "").toLowerCase();
    return lang === "en-in" || lang === "hi-in" || lang.slice(-3) === "-in";
  }

  function currencyFromCountry(code) {
    return String(code || "").toUpperCase() === "IN" ? "inr" : "usd";
  }

  function pickCurrency() {
    if (window.TFI_GEO_CURRENCY === "usd" || window.TFI_GEO_CURRENCY === "inr") {
      return window.TFI_GEO_CURRENCY;
    }

    var cachedCountry = read(COUNTRY_KEY);
    if (cachedCountry) return currencyFromCountry(cachedCountry);

    return indiaFromDevice() ? "inr" : "usd";
  }

  var currency = pickCurrency();
  if (currency === "usd" && !usdPricesReady()) currency = "inr";

  window.TFI_CURRENCY = currency;
  write(STORE_KEY, currency);

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

  function applyCountry(code) {
    if (!code || code === "XX" || code === "T1") return;
    write(COUNTRY_KEY, code);

    var next = currencyFromCountry(code);
    if (next === "usd" && !usdPricesReady()) next = "inr";
    if (next !== currency) {
      write(STORE_KEY, next);
      window.location.reload();
    }
  }

  function detectCountry() {
    if (window.TFI_GEO_COUNTRY) {
      applyCountry(window.TFI_GEO_COUNTRY);
      return;
    }
    if (read(COUNTRY_KEY)) return;

    fetch("https://ipwho.is/?fields=success,country_code")
      .then(function (res) { return res.json(); })
      .then(function (data) {
        if (data && data.success && data.country_code) applyCountry(data.country_code);
      })
      .catch(function () {});
  }

  detectCountry();
})();
