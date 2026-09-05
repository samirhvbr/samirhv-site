// GitHub View — dashboard: toggle log/linear das barras + expandir "mostrar
// todos os repos". Porte de bar_scale_controller.js + reveal_controller.js
// (Stimulus) para JS vanilla. Sem dependências.
(function () {
  function initBarScale(root) {
    var label = root.querySelector("[data-gh-barscale-label]");
    var bars = root.querySelectorAll("[data-gh-bar]");
    if (!label || !bars.length) return;
    var linear = false;
    label.addEventListener("click", function () {
      linear = !linear;
      bars.forEach(function (bar) {
        bar.style.width = linear ? bar.dataset.linearWidth : bar.dataset.logWidth;
      });
      label.textContent = linear ? "(linear scale)" : "(log scale)";
    });
  }

  function initReveal(root) {
    var btn = root.querySelector("[data-gh-reveal-toggle]");
    var content = root.querySelector("[data-gh-reveal-content]");
    if (!btn || !content) return;
    btn.addEventListener("click", function () {
      var hidden = content.classList.toggle("is-hidden");
      btn.textContent = hidden ? btn.dataset.more : btn.dataset.less;
    });
  }

  // Filtro client-side dos cards (porte de repo_filter_controller.js). Esconde
  // todo card cujo "owner/name" (data-repo-name) não contenha o texto digitado.
  function initFilter(root) {
    var input = root.querySelector("[data-gh-filter-input]");
    var items = root.querySelectorAll("[data-gh-filter-item]");
    var empty = root.querySelector("[data-gh-filter-empty]");
    if (!input || !items.length) return;
    input.addEventListener("input", function () {
      var q = input.value.trim().toLowerCase();
      var visible = 0;
      items.forEach(function (item) {
        var match = q === "" || (item.dataset.repoName || "").indexOf(q) !== -1;
        item.classList.toggle("is-hidden", !match);
        if (match) visible++;
      });
      if (empty) empty.classList.toggle("is-hidden", visible > 0);
    });
  }

  // Typeahead do add-form (porte de autocomplete_controller.js). Busca em
  // /suggestions e insere "owner/name". Escapa tudo via textContent (nunca innerHTML).
  function initAutocomplete(root) {
    var input = root.querySelector("[data-gh-autocomplete-input]");
    var list = root.querySelector("[data-gh-autocomplete-list]");
    var url = root.dataset.url;
    if (!input || !list || !url) return;
    var debounce;
    var activeIndex = -1;

    function hide() {
      list.classList.add("is-hidden");
      activeIndex = -1;
    }

    function pick(fullName) {
      input.value = fullName;
      hide();
      if (input.form && input.form.requestSubmit) input.form.requestSubmit();
      else if (input.form) input.form.submit();
    }

    function render(items) {
      activeIndex = -1;
      list.textContent = "";
      if (!items || !items.length) return hide();
      items.forEach(function (repo) {
        var btn = document.createElement("button");
        btn.type = "button";
        btn.className = "gh-ac__item";
        btn.dataset.fullName = repo.full_name;
        btn.addEventListener("click", function () { pick(repo.full_name); });

        var name = document.createElement("span");
        name.className = "gh-ac__name";
        name.textContent = repo.display_name || repo.full_name;
        btn.appendChild(name);

        if (repo.private) {
          var badge = document.createElement("span");
          badge.className = "gh-ac__badge";
          badge.textContent = "private";
          btn.appendChild(badge);
        }
        if (repo.description) {
          var desc = document.createElement("span");
          desc.className = "gh-ac__desc";
          desc.textContent = repo.description;
          btn.appendChild(desc);
        }
        list.appendChild(btn);
      });
      list.classList.remove("is-hidden");
    }

    function search() {
      clearTimeout(debounce);
      debounce = setTimeout(function () {
        var q = input.value.trim();
        fetch(url + "?q=" + encodeURIComponent(q), { headers: { Accept: "application/json" } })
          .then(function (r) { return r.ok ? r.json() : []; })
          .then(render)
          .catch(hide);
      }, 250);
    }

    input.addEventListener("input", search);
    input.addEventListener("focus", search);
    input.addEventListener("keydown", function (e) {
      var items = Array.prototype.slice.call(list.children);
      if (list.classList.contains("is-hidden") || !items.length) return;
      if (e.key === "ArrowDown" || e.key === "ArrowUp") {
        e.preventDefault();
        var step = e.key === "ArrowDown" ? 1 : -1;
        activeIndex = (activeIndex + step + items.length) % items.length;
        items.forEach(function (it, i) { it.classList.toggle("is-active", i === activeIndex); });
      } else if (e.key === "Enter" && activeIndex >= 0) {
        e.preventDefault();
        pick(items[activeIndex].dataset.fullName);
      } else if (e.key === "Escape") {
        hide();
      }
    });
    document.addEventListener("click", function (e) {
      if (!root.contains(e.target)) hide();
    });
  }

  // Acompanha um repo que está sincronizando até ele parar.
  //
  // O endpoint `admin.github-view.repos.status` existe desde o port do
  // github-visualize e o docblock dele sempre disse "o JS pergunta enquanto
  // está 'syncing'" — só que esse JS nunca foi escrito. Sem ele o cartão
  // congela em "syncing" até um F5.
  //
  // Regras: só cartões em 'syncing' entram (o Blade só põe data-url neles),
  // para de perguntar assim que o status muda, e desiste depois de alguns
  // erros seguidos em vez de bater no servidor para sempre.
  function initSyncBadge(badge) {
    var url = badge.dataset.url;
    var every = (parseInt(badge.dataset.every, 10) || 5) * 1000;
    var failures = 0;
    var timer = null;

    function stop() {
      if (timer) window.clearTimeout(timer);
      timer = null;
    }

    function settle(status) {
      stop();
      badge.className = "gh-badge gh-badge--" + status;
      badge.innerHTML =
        status === "synced"
          ? '<i class="fa-solid fa-circle-check"></i> synced'
          : status === "failed"
            ? '<i class="fa-solid fa-circle-exclamation"></i> failed'
            : '<i class="fa-solid fa-clock"></i> ' + status;
      delete badge.dataset.ghSync;
    }

    function tick() {
      // Uma aba escondida não precisa perguntar nada.
      if (document.hidden) {
        timer = window.setTimeout(tick, every);
        return;
      }

      fetch(url, { headers: { Accept: "application/json" }, credentials: "same-origin" })
        .then(function (r) {
          if (!r.ok) throw new Error(r.status);
          return r.json();
        })
        .then(function (data) {
          failures = 0;
          if (!data.syncing) {
            settle(data.sync_status || "synced");
            return;
          }
          timer = window.setTimeout(tick, every);
        })
        .catch(function () {
          // Três falhas seguidas: a sessão caiu, o repo sumiu ou a rede foi
          // embora. Insistir não conserta nenhuma das três.
          if (++failures >= 3) return stop();
          timer = window.setTimeout(tick, every * failures);
        });
    }

    timer = window.setTimeout(tick, every);
  }

  function init() {
    document.querySelectorAll("[data-gh-sync]").forEach(initSyncBadge);
    document.querySelectorAll("[data-gh-bars]").forEach(function (root) {
      initBarScale(root);
      initReveal(root);
    });
    document.querySelectorAll("[data-gh-filter]").forEach(initFilter);
    document.querySelectorAll("[data-gh-autocomplete]").forEach(initAutocomplete);
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})();
