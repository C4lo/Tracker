let initiativeList = [];
let currentTurnIndex = 0; // Aktueller Turn

// Funktion: Liste rendern
function renderList(containerId, list, isDM = false) {
  const listContainer = document.getElementById(containerId);
  listContainer.innerHTML = "";

  list
    .sort((a, b) => b.initiative - a.initiative)
    .forEach((entry, index) => {
      const card = document.createElement("div");
      card.className = `card ${index === currentTurnIndex ? "current" : ""}`;
      card.innerHTML = `
        <h3>${entry.name}</h3>
        <p>Initiative: ${entry.initiative}</p>
        <p>HP: ${entry.hp}</p>
        ${
          isDM
            ? `<p>Typ: ${entry.type}</p>
        <button onclick="editEntry(${entry.id})">Bearbeiten</button>
        <button onclick="deleteEntry(${entry.id})">Löschen</button>`
            : ""
        }
      `;

      listContainer.appendChild(card);
    });
}

// Spieleransicht
function renderInitiative() {
  console.log("renderInitiative() mit currentTurnIndex:", currentTurnIndex);
  renderList("initiative-list", initiativeList);
}

// DM-Ansicht
function renderDMTable() {
  renderList("initiative-list", initiativeList, true);
}

// Funktion: Nächster Zug
function nextTurn() {
  currentTurnIndex++;
  // Zurück zum Anfang, wenn das Ende erreicht ist
  if (currentTurnIndex >= initiativeList.length) {
    currentTurnIndex = 0;
  }
  // Aktualisiere die Anzeige
  renderInitiative();
  renderDMTable();

  // Synchronisiere mit allen Clients
  sendMessage("nextTurn", currentTurnIndex);
}

// Formularverarbeitung
function handleFormSubmit(event) {
  event.preventDefault();
  const name = document.getElementById("name").value;
  const initiative = parseInt(document.getElementById("initiative").value);
  const hp = document.getElementById("hp").value;
  const type = document.getElementById("type").value;

  const newEntry = { id: Date.now(), name, initiative, hp, type };
  initiativeList.push(newEntry);
  sendMessage("add", newEntry);
  event.target.reset();
  renderDMTable();
}

// Bearbeiten eines Eintrags
function editEntry(id) {
  const entry = initiativeList.find((item) => item.id === id);
  if (entry) {
    document.getElementById("name").value = entry.name;
    document.getElementById("initiative").value = entry.initiative;
    document.getElementById("hp").value = entry.hp;
    document.getElementById("type").value = entry.type;

    initiativeList = initiativeList.filter((item) => item.id !== id);
    sendMessage("delete", id);
    renderDMTable();
  }
}

// Löschen eines Eintrags
function deleteEntry(id) {
  initiativeList = initiativeList.filter((item) => item.id !== id);
  sendMessage("delete", id);
  renderDMTable();
}

// WebSocket-Verbindung aufbauen
const socket = new WebSocket("ws://192.168.2.40:8080");

// Verbindung geöffnet
socket.addEventListener("open", () => {
  console.log("Verbunden mit dem WebSocket-Server");
});

// Nachricht vom Server empfangen
socket.addEventListener("message", (event) => {
  const data = JSON.parse(event.data);

  switch (data.type) {
    case "init":
      initiativeList = data.payload;
      if (document.getElementById("initiative-form")) {
        renderDMTable();
      } else {
        renderInitiative();
      }
      break;

    case "update":
      initiativeList = data.payload;
      if (document.getElementById("initiative-form")) {
        renderDMTable();
      } else {
        renderInitiative();
      }
      break;

    case "nextTurn":
      console.log("Spieler empfängt nextTurn:", data.payload); //Debug-Log
      currentTurnIndex = data.payload;
      if (document.getElementById("initiative-form")) {
        console.log("Bin auf der DM-Seite, rufe renderDMTable() auf");
        renderDMTable();
      } else {
        console.log("Bin auf der Spieler-Seite, rufe renderInitiative() auf");
        renderInitiative();
      }
      break;

    default:
      console.error("Unbekannter Nachrichtentyp:", data.type);
  }
});

// Nachricht senden
function sendMessage(type, payload) {
  const message = JSON.stringify({ type, payload });
  socket.send(message);
}

// Event Listener
if (document.getElementById("initiative-form")) {
  document
    .getElementById("initiative-form")
    .addEventListener("submit", handleFormSubmit);
  document.addEventListener("DOMContentLoaded", () => {
    renderDMTable();
  });
} else if (document.getElementById("initiative-list")) {
  document.addEventListener("DOMContentLoaded", () => {
    renderInitiative();
  });
}
