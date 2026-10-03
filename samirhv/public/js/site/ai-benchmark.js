/* AI Benchmark page: the vendor filter above each leaderboard.

   It only hides rows. Every row keeps the rank it has in the full leaderboard,
   because the rank is the result and a filter is just a way of reading it.
   The bar is rendered hidden, so without JavaScript the page is the plain
   leaderboard and no control is left that does nothing. */
(function () {
  "use strict";

  function initFilter(bar) {
    var board = bar.nextElementSibling;
    while (board && !board.classList.contains("ab-board")) board = board.nextElementSibling;
    if (!board) return;

    var rows = Array.prototype.slice.call(board.querySelectorAll(":scope > li[data-vendor]"));
    var chips = Array.prototype.slice.call(bar.querySelectorAll(".ab-chip"));
    var allChip = bar.querySelector('.ab-chip[data-vendor=""]');
    var status = bar.querySelector(".ab-filter__status");
    var template = status ? status.getAttribute("data-template") : null;
    var selected = {};

    function apply() {
      var any = Object.keys(selected).length > 0;
      var shown = 0;
      rows.forEach(function (row) {
        var visible = !any || selected[row.getAttribute("data-vendor")] === true;
        row.hidden = !visible;
        if (visible) shown++;
      });
      chips.forEach(function (chip) {
        var vendor = chip.getAttribute("data-vendor");
        var on = vendor === "" ? !any : selected[vendor] === true;
        chip.setAttribute("aria-pressed", on ? "true" : "false");
      });
      if (status && template) {
        status.textContent = template.replace(":shown", String(shown)).replace(":total", String(rows.length));
      }
    }

    chips.forEach(function (chip) {
      chip.addEventListener("click", function () {
        var vendor = chip.getAttribute("data-vendor");
        if (vendor === "") {
          selected = {};
        } else if (selected[vendor]) {
          delete selected[vendor]; // the last one off means "all" again
        } else {
          selected[vendor] = true;
        }
        apply();
      });
    });

    bar.hidden = false;
    apply();
  }

  document.querySelectorAll("[data-ab-filter]").forEach(initFilter);
})();
