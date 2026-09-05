/* Public-site behaviour. Small on purpose: the theme is static and this is the
   only script the public pages load beyond Canvas itself. */
(function () {
  "use strict";

  // The projects dropdown.
  //
  // CSS already opens it on :hover and on :focus-within, so tabbing into it
  // works with JavaScript off. This adds what CSS cannot express: clicking the
  // trigger toggles it, Escape closes it and returns focus to the trigger, and
  // clicking away closes it. aria-expanded is kept truthful throughout, because
  // a screen reader reads that attribute and not the CSS.
  function initDropdown(trigger) {
    var parent = trigger.closest(".s-dd-parent");
    if (!parent) return;

    function setOpen(open) {
      trigger.setAttribute("aria-expanded", open ? "true" : "false");
    }

    trigger.addEventListener("click", function () {
      setOpen(trigger.getAttribute("aria-expanded") !== "true");
    });

    parent.addEventListener("keydown", function (e) {
      if (e.key !== "Escape") return;
      setOpen(false);
      trigger.focus();
    });

    // Focus leaving the menu entirely closes it. focusout fires before the new
    // element takes focus, so the check is deferred a tick.
    parent.addEventListener("focusout", function () {
      window.setTimeout(function () {
        if (!parent.contains(document.activeElement)) setOpen(false);
      }, 0);
    });

    document.addEventListener("click", function (e) {
      if (!parent.contains(e.target)) setOpen(false);
    });
  }

  function init() {
    document.querySelectorAll("[data-dd-trigger]").forEach(initDropdown);
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})();
