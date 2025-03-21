const API_URL = "/initiativeTracker/backend.php"; // Stelle sicher, dass der Pfad korrekt ist

// Holt die Initiative-Liste vom Server
async function fetchInitiativeList(mode = "session", fightName = "") {
  console.log(
    `📢 fetchInitiativeList() wurde aufgerufen! Mode: ${mode}, Fight: ${fightName}`
  );

  const url =
    mode === "session"
      ? "/initiativeTracker/backend.php?mode=session"
      : `/initiativeTracker/backend.php?mode=db&fight_name=${fightName}`;
  console.log("fetchInitiativeList() wurde aufgerufen mit:", mode, fightName);

  try {
    const response = await fetch(url);
    const data = await response.json();

    console.log("📩 API-Antwort erhalten:", data);

    if (data.success) {
      renderList(data.entries);
    } else {
      console.error("⚠ Fehler beim Laden der Liste:", data.message);
    }
  } catch (error) {
    console.error("❌ Netzwerkfehler:", error);
  }
}

// Funktion zum Rendern der Initiative-Tabelle
function renderList(entries) {
  const listContainer = document.getElementById("initiative-list");
  const existingEntries = listContainer.querySelectorAll(".initiative-card");

  // Entferne nur Einträge, die nicht mehr existieren sollten
  existingEntries.forEach((entry) => {
    const entryId = entry.getAttribute("data-id");
    if (!entries.some((e) => e.id == entryId)) {
      entry.remove();
    }
  });

  // Neue Einträge hinzufügen, die noch nicht existieren
  entries.forEach((entry, index) => {
    if (!listContainer.querySelector(`[data-id="${entry.id}"]`)) {
      const entryDiv = document.createElement("div");
      entryDiv.className =
        "initiative-card bg-zinc-800 text-white p-4 rounded-lg shadow-md border-2 border-blue-400";
      entryDiv.setAttribute("data-id", entry.id);

      // Falls dieser Charakter gerade am Zug ist, hervorheben
      if (index === currentTurnIndex) {
        entryDiv.classList.add("border-yellow-400");
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
    }
  });

  console.log("✅ Liste aktualisiert!");
}

// Fügt einen neuen Eintrag hinzu
async function addEntry(event, mode = "session", fightName = "") {
  event.preventDefault();
  console.log(
    `🛠 addEntry() wurde aufgerufen! Mode: ${mode}, Fight: ${fightName}`
  );

  const name = document.getElementById("name").value;
  const initiative = parseInt(document.getElementById("initiative").value);
  const hp = document.getElementById("hp").value;
  const type = document.getElementById("type").value;

  const entry = { mode, fight_name: fightName, name, initiative, hp, type };

  try {
    const response = await fetch("/initiativeTracker/backend.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(entry),
    });

    const result = await response.json();

    if (result.success) {
      console.log("✅ Neuer Eintrag erfolgreich gespeichert!");
      fetchInitiativeList(mode, fightName); // Aktualisiere die Liste
      document.getElementById("initiative-form").reset();
    } else {
      console.error("⚠ Fehler beim Speichern:", result.message);
    }
  } catch (error) {
    console.error("❌ Netzwerkfehler:", error);
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
let currentTurnIndex = 0; // Speichert, wer gerade am Zug ist

function nextTurn() {
  const entries = document.querySelectorAll(".initiative-card"); // Alle Karten holen

  if (entries.length === 0) return; // Falls keine Einträge existieren, nichts tun

  // Entferne den Rahmen vom aktuellen Charakter
  entries.forEach((entry) => entry.classList.remove("border-yellow-400"));

  // Gehe zum nächsten Charakter, oder starte von vorne
  currentTurnIndex = (currentTurnIndex + 1) % entries.length;

  // Setze den neuen Rahmen
  entries[currentTurnIndex].classList.add("border-yellow-400");

  console.log(
    `🔥 Nächster Zug: ${
      entries[currentTurnIndex].querySelector("h3").innerText
    }`
  );
}
function nextTurn() {
  const entries = document.querySelectorAll(".initiative-card");
  if (entries.length === 0) return;

  let activeIndex = -1;
  entries.forEach((entry, index) => {
    if (entry.classList.contains("border-yellow-400")) {
      activeIndex = index;
      entry.classList.remove("border-yellow-400");
    }
  });

  const nextIndex = (activeIndex + 1) % entries.length;
  entries[nextIndex].classList.add("border-yellow-400");
}

// Event Listener für das Formular
document.addEventListener("DOMContentLoaded", function () {
  document
    .getElementById("initiative-form")
    .addEventListener("submit", function (event) {
      event.preventDefault(); // Standard-Submit verhindern

      const mode = document.getElementById("fight_mode").value;
      const fightName = document.getElementById("fight_name").value;
      addEntry(event, mode, fightName);
    });
});

// Lade Daten beim Start
document.addEventListener("DOMContentLoaded", fetchInitiativeList);
