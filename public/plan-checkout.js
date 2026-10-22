(function () {
  "use strict";

  var busy = false;
  var pendingKey = "tfi-pending-purchase";

  function setBusy(trigger, value) {
    if (!trigger) return;
    trigger.setAttribute("aria-disabled", String(value));
    trigger.classList.toggle("is-loading", value);
  }

  function showError(message, trigger) {
    var form = trigger && trigger.closest ? trigger.closest("form") : null;
    var area = form && form.querySelector("#mvError");

    if (!area && trigger && trigger.parentNode) {
      area = trigger.parentNode.querySelector(".tfi-checkout-error");
      if (!area) {
        area = document.createElement("p");
        area.className = "tfi-checkout-error";
        area.setAttribute("role", "alert");
        trigger.parentNode.insertBefore(area, trigger.nextSibling);
      }
    }

    if (area) {
      area.textContent = message;
      area.classList.add("show");
    } else {
      window.alert(message);
    }
  }

  function finish(trigger, options) {
    busy = false;
    setBusy(trigger, false);
    if (options && typeof options.onState === "function") options.onState(false);
  }

  function loadRazorpay() {
    if (window.Razorpay) return Promise.resolve();

    return new Promise(function (resolve, reject) {
      var script = document.querySelector('script[data-tfi-razorpay]');
      if (script) {
        script.addEventListener("load", resolve, { once: true });
        script.addEventListener("error", reject, { once: true });
        return;
      }

      script = document.createElement("script");
      script.src = "https://checkout.razorpay.com/v1/checkout.js";
      script.async = true;
      script.setAttribute("data-tfi-razorpay", "");
      script.onload = resolve;
      script.onerror = function () {
        reject(new Error("Payment service could not load. Check your connection and try again."));
      };
      document.head.appendChild(script);
    });
  }

  function post(path, payload) {
    return fetch(path, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "Accept": "application/json"
      },
      body: JSON.stringify(payload)
    }).then(function (response) {
      return response.json().catch(function () {
        return {};
      }).then(function (data) {
        if (!response.ok) {
          var error = new Error(data.error || "Could not complete checkout. Please try again.");
          error.status = response.status;
          throw error;
        }
        return data;
      });
    });
  }

  function loginForPurchase(selection) {
    try {
      window.sessionStorage.setItem(pendingKey, JSON.stringify(selection));
    } catch (error) {
      showError("Please log in as a customer, then select your plan again.");
      window.location.href = "/login";
      return;
    }

    var redirect = window.location.pathname + window.location.search + window.location.hash;
    window.location.href = "/login?redirect=" + encodeURIComponent(redirect);
  }

  function start(selection, options) {
    if (busy) return;
    options = options || {};
    var trigger = options.trigger || null;
    busy = true;
    setBusy(trigger, true);
    if (typeof options.onState === "function") options.onState(true);

    loadRazorpay().then(function () {
      return post("/api/razorpay/create-order", selection);
    }).then(function (order) {
      var verifying = false;
      var checkout = new window.Razorpay({
        key: order.key_id,
        order_id: order.order_id,
        amount: order.amount,
        currency: order.currency,
        name: "TFI — Time To Fit India",
        description: order.description,
        image: "/logo/logo.png",
        theme: { color: "#e63946" },
        modal: {
          ondismiss: function () {
            if (!verifying) finish(trigger, options);
          }
        },
        handler: function (payment) {
          verifying = true;
          post("/api/razorpay/verify", {
            razorpay_order_id: payment.razorpay_order_id,
            razorpay_payment_id: payment.razorpay_payment_id,
            razorpay_signature: payment.razorpay_signature
          }).then(function () {
            window.location.href = "/payment-success?payment_id=" +
              encodeURIComponent(payment.razorpay_payment_id);
          }).catch(function (error) {
            finish(trigger, options);
            reportError(
              error.status === 403
                ? error.message
                : "We received your payment but could not confirm it. Please do not pay again; contact support with payment ID " +
                  payment.razorpay_payment_id + ".",
              options
            );
          });
        }
      });

      checkout.on("payment.failed", function (event) {
        finish(trigger, options);
        reportError(
          (event && event.error && event.error.description) ||
          "Payment failed. Please try again.",
          options
        );
      });
      checkout.open();
    }).catch(function (error) {
      finish(trigger, options);
      if (error.status === 401) {
        loginForPurchase(selection);
        return;
      }
      reportError(error.message, options);
    });
  }

  function reportError(message, options) {
    if (options && typeof options.onError === "function") {
      options.onError(message);
      return;
    }
    showError(message, options && options.trigger);
  }

  window.TfiCheckout = {
    start: start,
    loginForPurchase: loginForPurchase
  };

  try {
    var pending = window.sessionStorage.getItem(pendingKey);
    if (pending) {
      window.sessionStorage.removeItem(pendingKey);
      start(JSON.parse(pending), null);
    }
  } catch (error) {
    return;
  }
})();
