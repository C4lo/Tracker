// API URL für Backend-Kommunikation
const API_URL = "/initiativeTracker/backend.php";

// Funktion zum Laden der Initiative-Liste
document.addEventListener("DOMContentLoaded", () => {
  fetchInitiativeList();

  const form = document.getElementById("initiative-form");
  if (form) {
    form.addEventListener("submit", (event) => {
      event.preventDefault();
      const mode = document.getElementById("fight_mode").value;
      const fightName = document.getElementById("fight_name").value;
      addEntry(event, mode, fightName);
    });
  }
});

// Holt die Initiative-Liste vom Server und rendert sie
async function fetchInitiativeList(mode = "session", fightName = "") {
  const url =
    mode === "session"
      ? `${API_URL}?mode=session`
      : `${API_URL}?mode=db&fight_name=${fightName}`;

  try {
    const response = await fetch(url);
    const data = await response.json();
    if (data.success) {
      renderList(data.entries);
    } else {
      console.error("Fehler beim Laden der Initiative-Liste:", data.message);
    }
  } catch (error) {
    console.error("Netzwerkfehler beim Laden der Initiative-Liste:", error);
  }
}

// Fügt einen neuen Eintrag hinzu
async function addEntry(event, mode = "session", fightName = "") {
  event.preventDefault();
  const name = document.getElementById("name").value;
  const initiative = parseInt(document.getElementById("initiative").value);
  const hp = document.getElementById("hp").value;
  const type = document.getElementById("type").value;

  const entry = { mode, fight_name: fightName, name, initiative, hp, type };

  try {
    const response = await fetch(API_URL, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(entry),
    });
    const result = await response.json();
    if (result.success) fetchInitiativeList(mode, fightName);
    else console.error("Fehler beim Hinzufügen des Eintrags:", result.message);
  } catch (error) {
    console.error("Netzwerkfehler beim Hinzufügen eines Eintrags:", error);
  }
}

// Löscht einen Eintrag über die API
async function deleteEntry(id, mode = "session", fightName = "") {
  try {
    const response = await fetch(API_URL, {
      method: "DELETE",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ id, mode, fight_name: fightName }),
    });
    const result = await response.json();
    if (result.success) fetchInitiativeList(mode, fightName);
    else console.error("Fehler beim Löschen des Eintrags:", result.message);
  } catch (error) {
    console.error("Netzwerkfehler beim Löschen eines Eintrags:", error);
  }
}

// Rendert die Liste der Initiative-Einträge
function renderList(entries) {
  const listContainer = document.getElementById("initiative-list");
  listContainer.innerHTML = "";

  if (!entries || entries.length === 0) {
    listContainer.innerHTML = "<p class='text-gray-400'>Keine Einträge</p>";
    return;
  }

  entries.forEach((entry) => {
    const entryDiv = document.createElement("div");
    entryDiv.className =
      "initiative-card bg-zinc-800 text-white p-4 rounded-lg shadow-md border-2 border-blue-400";
    entryDiv.setAttribute("data-id", entry.id);
    entryDiv.innerHTML = `
            <h3 class="text-lg font-bold">${entry.name}</h3>
            <p>Initiative: ${entry.initiative}</p>
            <p>HP: ${entry.hpStatus}</p>
            <button class="mt-2 bg-red-600 text-white px-2 py-1 rounded-md hover:bg-red-800 transition" onclick="deleteEntry(${entry.id})">
                Löschen
            </button>
        `;
    listContainer.appendChild(entryDiv);
  });
}
