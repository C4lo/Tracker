// js/script.js — vollständiger Ersatz

document.addEventListener("DOMContentLoaded", () => {
  initApp();
});

/* =========================
   App Bootstrap
   ========================= */
async function initApp() {
  try {
    const session = await checkSession();
    if (!session.loggedIn) {
      window.location.href = "login.html";
      return;
    }

    // Username anzeigen
    const userDisplay = document.getElementById("username-display");
    if (userDisplay) userDisplay.textContent = session.username || "User";

    // Logout
    const logoutBtn = document.getElementById("logout-button");
    if (logoutBtn) {
      logoutBtn.addEventListener("click", async () => {
        try {
          await safeFetch("backend/logout.php", { method: "POST" });
        } catch (_) {}
        window.location.href = "login.html";
      });
    }

    // Rolle auswerten und Views schalten
    const role = session.role || "player";
    const hasPlayerView = !!document.getElementById("player-view");
    const hasGmView = !!document.getElementById("gm-view");

    if (role === "player" && hasPlayerView) {
      showPlayerView();
      initPlayerView();
    } else {
      // Fallback: Wenn kein GM-Container vorhanden ist, zeige notfalls Player-Container
      if (hasGmView) {
        showGmView();
        initGmView();
        initGmExtras(); // GM-Inbox
      } else if (hasPlayerView) {
        showPlayerView();
        initPlayerView();
      }
    }
  } catch (e) {
    console.error(e);
    window.location.href = "login.html";
  }
}

/* =========================
   Helpers
   ========================= */
async function checkSession() {
  const res = await safeFetch("backend/check_sessions.php");
  return res.json();
}

async function safeFetch(url, opts = {}) {
  const res = await fetch(url, opts);
  if (!res.ok) {
    const text = await res.text().catch(() => "");
    const err = new Error(`HTTP ${res.status} @ ${url}: ${text}`);
    err.status = res.status;
    throw err;
  }
  return res;
}

function showPlayerView() {
  const pv = document.getElementById("player-view");
  const gv = document.getElementById("gm-view");
  if (gv) gv.style.display = "none";
  if (pv) pv.style.display = "block";
}

function showGmView() {
  const pv = document.getElementById("player-view");
  const gv = document.getElementById("gm-view");
  if (pv) pv.style.display = "none";
  if (gv) gv.style.display = "block";
}

function escapeHtml(str) {
  return String(str)
    .replaceAll("&", "&amp;")
    .replaceAll("<", "&lt;")
    .replaceAll(">", "&gt;")
    .replaceAll('"', "&quot;")
    .replaceAll("'", "&#039;");
}

/* =========================
   PLAYER VIEW
   ========================= */
function initPlayerView() {
  const form = document.getElementById("player-submit-form");
  const list = document.getElementById("my-submissions");
  if (!form) return;

  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    const name = (document.getElementById("pname")?.value || "").trim();
    const initStr = document.getElementById("pinitiative")?.value || "";
    const initiative = parseInt(initStr, 10);

    if (!name || Number.isNaN(initiative)) {
      alert("Bitte Name und Initiative angeben.");
      return;
    }

    const fd = new FormData();
    fd.append("name", name);
    fd.append("initiative", initiative);

    try {
      const res = await safeFetch("backend/submit_initiative.php", {
        method: "POST",
        body: fd,
      });
      const data = await res.json();
      if (data.ok) {
        form.reset();
        await loadMySubmissions(list);
        alert("Submitted!");
      } else {
        alert(data.error || "Submit failed.");
      }
    } catch (err) {
      console.error(err);
      alert("Netzwerkfehler beim Senden.");
    }
  });

  loadMySubmissions(list).catch(console.error);
}

async function loadMySubmissions(listEl) {
  if (!listEl) return;
  listEl.innerHTML = "<li>Loading…</li>";
  try {
    const res = await safeFetch("backend/list_my_submissions.php");
    const data = await res.json();
    listEl.innerHTML = "";
    if (!data.ok || !Array.isArray(data.items) || data.items.length === 0) {
      listEl.innerHTML = "<li>No entries yet</li>";
      return;
    }
    data.items.forEach((item) => {
      const li = document.createElement("li");
      li.textContent = `${item.name} — ${item.initiative}  ${
        item.processed ? "(imported)" : "(pending)"
      }`;
      listEl.appendChild(li);
    });
  } catch (e) {
    console.error(e);
    listEl.innerHTML = "<li>Error loading submissions</li>";
  }
}

/* =========================
   GM VIEW — Tracker
   ========================= */
// In-Memory State (kann später durch DB ersetzt werden)
const trackerState = {
  entries: [], // { id, name, initiative, isActive }
  activeIndex: -1,
  nextId: 1,
};

function initGmView() {
  const form = document.getElementById("add-character-form");
  const list = document.getElementById("character-list");
  const btnNext = document.getElementById("next-turn");
  const btnSort = document.getElementById("sort");
  const btnClear = document.getElementById("clear-all");

  if (!form || !list) return;

  form.addEventListener("submit", (e) => {
    e.preventDefault();
    const name = (document.getElementById("name")?.value || "").trim();
    const initStr = document.getElementById("initiative")?.value || "";
    const initiative = parseInt(initStr, 10);
    if (!name || Number.isNaN(initiative)) return;

    addCharacterToList(name, initiative);
    form.reset();
  });

  if (btnNext) btnNext.addEventListener("click", nextTurn);
  if (btnSort) btnSort.addEventListener("click", sortByInitiative);
  if (btnClear) btnClear.addEventListener("click", clearAll);

  renderList(list);
}

function addCharacterToList(name, initiative) {
  const entry = {
    id: trackerState.nextId++,
    name,
    initiative,
    isActive: false,
  };
  trackerState.entries.push(entry);

  if (trackerState.activeIndex === -1) {
    trackerState.activeIndex = 0;
    trackerState.entries[0].isActive = true;
  }

  renderList();
}

function renderList(forcedListEl) {
  const list = forcedListEl || document.getElementById("character-list");
  if (!list) return;

  list.innerHTML = "";
  trackerState.entries.forEach((e, idx) => {
    const li = document.createElement("li");
    li.textContent = `${e.name} — ${e.initiative}`;
    if (idx === trackerState.activeIndex) {
      li.style.fontWeight = "700";
      li.style.textDecoration = "underline";
    }
    list.appendChild(li);
  });
}

function sortByInitiative() {
  trackerState.entries.sort((a, b) => b.initiative - a.initiative);

  if (trackerState.activeIndex !== -1) {
    const activeId = trackerState.entries[trackerState.activeIndex]?.id;
    const newIndex = trackerState.entries.findIndex((e) => e.id === activeId);
    trackerState.activeIndex =
      newIndex === -1 ? (trackerState.entries[0] ? 0 : -1) : newIndex;
  }

  renderList();
}

function nextTurn() {
  if (trackerState.entries.length === 0) return;
  if (trackerState.activeIndex === -1) {
    trackerState.activeIndex = 0;
  } else {
    trackerState.activeIndex =
      (trackerState.activeIndex + 1) % trackerState.entries.length;
  }
  renderList();
}

function clearAll() {
  trackerState.entries = [];
  trackerState.activeIndex = -1;
  renderList();
}

/* =========================
   GM VIEW — Inbox (Player Submissions)
   ========================= */
function initGmExtras() {
  const inboxBtn = document.getElementById("refresh-inbox");
  const inboxList = document.getElementById("inbox-list");
  if (!inboxBtn || !inboxList) return;

  inboxBtn.addEventListener("click", () => loadInbox(inboxList));
  loadInbox(inboxList);
}

async function loadInbox(listEl) {
  if (!listEl) return;
  listEl.innerHTML = "<li>Loading…</li>";

  try {
    const res = await safeFetch("backend/list_pending_submissions.php");
    const data = await res.json();

    listEl.innerHTML = "";
    if (!data.ok || !Array.isArray(data.items) || data.items.length === 0) {
      listEl.innerHTML = "<li>No pending submissions</li>";
      return;
    }

    data.items.forEach((item) => {
      const li = document.createElement("li");
      li.innerHTML = `
        <strong>${escapeHtml(item.name)}</strong> — ${Number(item.initiative)}
        <em> by ${escapeHtml(item.username)}</em>
        <button
          class="button"
          data-id="${item.id}"
          data-name="${escapeHtml(item.name)}"
          data-init="${Number(item.initiative)}"
        >
          Add to Tracker
        </button>
      `;
      listEl.appendChild(li);
    });

    listEl.querySelectorAll("button[data-id]").forEach((btn) => {
      btn.addEventListener("click", async () => {
        const id = parseInt(btn.getAttribute("data-id"), 10);
        const name = btn.getAttribute("data-name");
        const initiative = parseInt(btn.getAttribute("data-init"), 10);

        // 1) In Tracker übernehmen
        addCharacterToList(name, initiative);

        // 2) Serverseitig als verarbeitet markieren
        const fd = new FormData();
        fd.append("id", id);
        try {
          await safeFetch("backend/mark_submission_processed.php", {
            method: "POST",
            body: fd,
          });
          await loadInbox(listEl);
        } catch (e) {
          console.error(e);
          alert("Konnte Submission nicht als verarbeitet markieren.");
        }
      });
    });
  } catch (e) {
    console.error(e);
    listEl.innerHTML = "<li>Error loading inbox</li>";
  }
}
