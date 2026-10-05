/* AI Benchmark page: the vendor filter, the category order, the pages of
   each leaderboard and the card that opens on a click. The same file runs on samirhv.com.br and shvia.org.

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
    var pagerTpl = text(bar.querySelector(".ab-pager__tpl"));
    var selected = {};
    var sortKey = "";
    var PAGE = 25;
    var page = 1;
    var pager = null;

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
        var match = !any || selected[row.getAttribute("data-vendor")] === true;
        row.setAttribute("data-match", match ? "1" : "0");
        if (match) shown++;
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

    // Pages of PAGE rows over the rows the filter keeps, in their current order.
    // Hiding is done here only, so the filter and the order never fight over it.
    function paginate() {
      var kept = Array.prototype.filter.call(board.children, function (row) {
        return row.getAttribute("data-match") !== "0";
      });
      var pages = Math.max(1, Math.ceil(kept.length / PAGE));
      if (page > pages) page = pages;
      rows.forEach(function (row) { row.hidden = true; });
      kept.slice((page - 1) * PAGE, page * PAGE).forEach(function (row) { row.hidden = false; });
      if (!pager) return;
      pager.hidden = pages === 1;
      pager.querySelector(".ab-pager__label").textContent = (pagerTpl || ":n / :total").replace(":n", String(page)).replace(":total", String(pages));
      pager.querySelector("[data-page=prev]").disabled = page === 1;
      pager.querySelector("[data-page=next]").disabled = page === pages;
    }

    function buildPager() {
      pager = document.createElement("nav");
      pager.className = "ab-pager";
      function btn(dir, label) {
        var b = document.createElement("button");
        b.type = "button";
        b.className = "ab-chip ab-pager__btn";
        b.setAttribute("data-page", dir);
        b.textContent = label;
        b.addEventListener("click", function () {
          page += dir === "next" ? 1 : -1;
          paginate();
          var reduce = window.matchMedia && window.matchMedia("(prefers-reduced-motion: reduce)").matches;
          bar.scrollIntoView({ block: "start", behavior: reduce ? "auto" : "smooth" });
        });
        return b;
      }
      var label = document.createElement("span");
      label.className = "ab-pager__label";
      label.setAttribute("aria-live", "polite");
      pager.appendChild(btn("prev", text(bar.querySelector(".ab-pager__prev")) || "‹"));
      pager.appendChild(label);
      pager.appendChild(btn("next", text(bar.querySelector(".ab-pager__next")) || "›"));
      board.parentNode.insertBefore(pager, board.nextSibling);
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
        page = 1;
        paginate();
      });
    });

    sortChips.forEach(function (chip) {
      chip.addEventListener("click", function () {
        sortKey = chip.getAttribute("data-sort");
        applySort();
        page = 1;
        paginate();
      });
    });

    // A click anywhere on a card opens its comment and details; links, controls and a
    // text selection keep their own behaviour, and the summary stays the keyboard control.
    rows.forEach(function (row) {
      var more = row.querySelector(".ab-row__more");
      if (!more) return;
      row.setAttribute("data-more", "");
      row.addEventListener("click", function (ev) {
        if (ev.target.closest("a, button, summary, .ab-row__more")) return;
        var sel = window.getSelection && window.getSelection();
        if (sel && String(sel).length) return;
        more.open = !more.open;
      });
    });

    bar.hidden = false;
    buildPager();
    applyFilter();
    applySort();
    paginate();
  }

  document.querySelectorAll("[data-ab-filter]").forEach(initBoard);
})();
