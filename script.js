const API_URL = "/initiativeTracker/backend.php"; // Stelle sicher, dass der Pfad korrekt ist

// Holt die Initiative-Liste vom Server
async function fetchInitiativeList() {
  const url = `${API_URL}?mode=session`;

  try {
    const response = await fetch(url);
    const data = await response.json();

    if (data.success) {
      renderList(data.entries);
    } else {
      console.error("Fehler beim Laden der Liste:", data.message);
    }
  } catch (error) {
    console.error("Netzwerkfehler:", error);
  }
}

// Funktion zum Rendern der Initiative-Tabelle
let currentTurnId = null;

function renderList(entries) {
  entries.sort((a, b) => b.initiative - a.initiative);

  const listContainer = document.getElementById("initiative-list");
  listContainer.innerHTML = "";

  entries.forEach((entry, index) => {
    const entryDiv = document.createElement("div");
    entryDiv.className =
      "initiative-card bg-zinc-800 text-white p-4 rounded-lg shadow-md border-2 border-blue-400";
    entryDiv.setAttribute("data-id", entry.id);

    if (entry.id === currentTurnId || (currentTurnId === null && index === 0)) {
      entryDiv.classList.add("ring-2", "ring-yellow-400", "animate-glow-pulse");
      currentTurnId = entry.id;
    }

    entryDiv.innerHTML = `
      <h3 class="text-lg font-bold">${entry.name}</h3>
      <p>Initiative: ${entry.initiative}</p>
      <p>HP: ${entry.hp}</p>
      <button class="mt-2 bg-red-600 text-white px-2 py-1 rounded-md hover:bg-red-800 transition" onclick="deleteEntry(${entry.id})">
        Löschen
      </button>
    `;

    listContainer.appendChild(entryDiv);
  });
}

// Fügt einen neuen Eintrag hinzu
async function addEntry(event) {
  event.preventDefault();

  const hpEl = document.getElementById("hp");
  const typeEl = document.getElementById("type");
  const modeEl = document.getElementById("fight_mode");
  const nameEl = document.getElementById("fight_name");
  const data = {
    name: document.getElementById("name").value,
    initiative: parseInt(document.getElementById("initiative").value),
    hp: hpEl ? hpEl.value : null,
    type: typeEl ? typeEl.value : null,
    fight_mode: modeEl ? modeEl.value : null,
    fight_name: nameEl ? nameEl.value || null : null,
  };

  try {
    const response = await fetch(API_URL, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(data),
    });

    const result = await response.json();
    if (result.success) {
      fetchInitiativeList(); // Aktualisiere die Liste
      document.getElementById("initiative-form").reset();
    } else {
      console.error("Fehler beim Speichern:", result.message);
    }
  } catch (error) {
    console.error("Netzwerkfehler:", error);
  }
}

// Löscht einen Eintrag über die API
async function deleteEntry(id) {
  try {
    const response = await fetch(API_URL, {
      method: "DELETE",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ id }),
    });
    const result = await response.json();

    if (result.success) {
      fetchInitiativeList();
    } else {
      console.error("Fehler beim Löschen:", result.message);
    }
  } catch (error) {
    console.error("Netzwerkfehler:", error);
  }
}

//verschiebt den goldenen Rand
function nextTurn() {
  const entries = document.querySelectorAll(".initiative-card");
  if (entries.length === 0) return;

  let currentIndex = -1;
  entries.forEach((entry, index) => {
    entry.classList.remove("ring-2", "ring-yellow-400", "animate-glow-pulse");

    if (entry.getAttribute("data-id") == currentTurnId) {
      currentIndex = index;
    }
  });

  const nextIndex = (currentIndex + 1) % entries.length;
  const nextEntry = entries[nextIndex];
  nextEntry.classList.add("ring-2", "ring-yellow-400", "animate-glow-pulse");

  currentTurnId = parseInt(nextEntry.getAttribute("data-id"));
}

// Event Listener für das Formular
document.addEventListener("DOMContentLoaded", () => {
  document
    .getElementById("initiative-form")
    .addEventListener("submit", addEntry);

  fetchInitiativeList(); // Lade Liste beim Start

  //Live-Update alle 5s
  setInterval(() => {
    fetchInitiativeList();
  }, 5000);
});
