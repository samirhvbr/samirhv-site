/* AI Benchmark page: the vendor filter and the category order above each
   leaderboard. The same file runs on samirhv.com.br and shvia.org.

   The filter only hides rows and the order only moves them. The number on each
   card stays its rank in the full leaderboard, because the rank is the result;
   ordering by a category adds the card's position in that category, counted
   over every agent, so it does not change with the filter either.
   The bar is rendered hidden, so without JavaScript the page is the plain
   leaderboard and no control is left that does nothing. */
(function () {
  "use strict";

  function initBoard(bar) {
    var board = bar.nextElementSibling;
    while (board && !board.classList.contains("ab-board")) board = board.nextElementSibling;
    if (!board) return;

    var rows = Array.prototype.slice.call(board.querySelectorAll(":scope > li[data-vendor]"));
    var vendorChips = Array.prototype.slice.call(bar.querySelectorAll(".ab-chip[data-vendor]"));
    var sortChips = Array.prototype.slice.call(bar.querySelectorAll(".ab-chip[data-sort]"));
    var status = bar.querySelector(".ab-filter__status");
    var shownTpl = text(bar.querySelector(".ab-filter__tpl"));
    var posTpl = text(bar.querySelector(".ab-sort__tpl"));
    var selected = {};
    var sortKey = "";

    function text(el) { return el ? el.textContent : null; }

    // "SEC:250 ARCH:50 …" → { SEC: 250, ARCH: 50, … }, read once per row.
    var scores = rows.map(function (row) {
      var out = {};
      (row.getAttribute("data-scores") || "").split(" ").forEach(function (pair) {
        var kv = pair.split(":");
        if (kv.length === 2) out[kv[0]] = Number(kv[1]);
      });
      return out;
    });

    function applyFilter() {
      var any = Object.keys(selected).length > 0;
      var shown = 0;
      rows.forEach(function (row) {
        var visible = !any || selected[row.getAttribute("data-vendor")] === true;
        row.hidden = !visible;
        if (visible) shown++;
      });
      vendorChips.forEach(function (chip) {
        var vendor = chip.getAttribute("data-vendor");
        chip.setAttribute("aria-pressed", (vendor === "" ? !any : selected[vendor] === true) ? "true" : "false");
      });
      if (status && shownTpl) {
        status.textContent = shownTpl.replace(":shown", String(shown)).replace(":total", String(rows.length));
      }
    }

    function applySort() {
      // Rows are rendered in rank order, so the index is the tie-break.
      var order = rows.map(function (row, i) { return i; });
      if (sortKey) {
        order.sort(function (a, b) {
          return (scores[b][sortKey] || 0) - (scores[a][sortKey] || 0) || a - b;
        });
      }
      var label = "";
      sortChips.forEach(function (chip) {
        var on = chip.getAttribute("data-sort") === sortKey;
        chip.setAttribute("aria-pressed", on ? "true" : "false");
        if (on && sortKey) label = chip.textContent.trim();
      });
      // Competition ranking: equal scores share a position (1, 1, 3).
      var pos = 0;
      order.forEach(function (idx, n) {
        var row = rows[idx];
        if (n === 0 || scores[idx][sortKey] !== scores[order[n - 1]][sortKey]) pos = n + 1;
        var badge = row.querySelector(".ab-row__catpos");
        if (badge) {
          badge.hidden = !sortKey;
          badge.textContent = sortKey && posTpl ? posTpl.replace(":pos", String(pos)).replace(":cat", label) : "";
        }
        Array.prototype.forEach.call(row.querySelectorAll("[data-cat]"), function (li) {
          li.classList.toggle("is-sorted", li.getAttribute("data-cat") === sortKey);
        });
        board.appendChild(row);
      });
    }

    vendorChips.forEach(function (chip) {
      chip.addEventListener("click", function () {
        var vendor = chip.getAttribute("data-vendor");
        if (vendor === "") {
          selected = {};
        } else if (selected[vendor]) {
          delete selected[vendor]; // the last one off means "all" again
        } else {
          selected[vendor] = true;
        }
        applyFilter();
      });
    });

    sortChips.forEach(function (chip) {
      chip.addEventListener("click", function () {
        sortKey = chip.getAttribute("data-sort");
        applySort();
      });
    });

    bar.hidden = false;
    applyFilter();
    applySort();
  }

  document.querySelectorAll("[data-ab-filter]").forEach(initBoard);
})();
