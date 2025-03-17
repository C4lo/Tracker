// Holt die Initiative-Liste vom Server
const API_URL = "/initiativeTracker/backend.php"; // Stelle sicher, dass der Pfad korrekt ist

// Holt die Initiative-Liste vom Server und zeigt sie an
async function fetchInitiativeList() {
  console.log("📢 fetchInitiativeList() wurde aufgerufen!");

  try {
    const response = await fetch(API_URL);
    const data = await response.json();

    console.log("📩 API-Antwort erhalten:", data); // Debugging

    if (data.success) {
      renderList("initiative-list", data.entries, true);
    } else {
      console.error("Fehler beim Laden der Daten:", data.message);
    }
  } catch (error) {
    console.error("❌ Netzwerkfehler:", error);
  }
}

// Funktion zum Rendern der Initiative-Tabelle
function renderList(containerId, list, isDM = false) {
  const listContainer = document.getElementById(containerId);
  listContainer.innerHTML = ""; // Alte Liste leeren

  list
    .sort((a, b) => b.initiative - a.initiative)
    .forEach((entry) => {
      const card = document.createElement("div");
      card.className = "card";
      card.innerHTML = `
            <h3>${entry.name}</h3>
            <p>Initiative: ${entry.initiative}</p>
            <p>HP: ${entry.hp}</p>
            ${
              isDM
                ? `<button onclick="deleteEntry(${entry.id})">Löschen</button>`
                : ""
            }
        `;
      listContainer.appendChild(card);
    });
}

// Daten nach dem Laden der Seite abrufen
document.addEventListener("DOMContentLoaded", fetchInitiativeList);

// Fügt einen neuen Eintrag hinzu
async function addEntry(event) {
  event.preventDefault();

  const name = document.getElementById("name").value;
  const initiative = parseInt(document.getElementById("initiative").value);
  const hp = document.getElementById("hp").value;
  const type = document.getElementById("type").value;

  const entry = { name, initiative, hp, type };

  try {
    const response = await fetch(API_URL, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(entry),
    });
    const result = await response.json();

    if (result.success) {
      fetchInitiativeList(); // Liste neu laden
      document.getElementById("initiative-form").reset();
    } else {
      console.error("Fehler beim Speichern:", result.message);
    }
  } catch (error) {
    console.error("Netzwerkfehler:", error);
  }
}

// Löscht einen Eintrag
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
      fetchInitiativeList(); // Liste nach dem Löschen neu laden
    } else {
      console.error("Fehler beim Löschen:", result.message);
    }
  } catch (error) {
    console.error("Netzwerkfehler:", error);
  }
}



// Event Listener für das Formular
if (document.getElementById("initiative-form")) {
  document
    .getElementById("initiative-form")
    .addEventListener("submit", addEntry);
}

// Lade Daten beim Start
document.addEventListener("DOMContentLoaded", fetchInitiativeList);
