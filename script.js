let initiativeList = [];

// Funktion: Liste rendern
function renderList(containerId, list, isDM = false) {
  const listContainer = document.getElementById(containerId);
  listContainer.innerHTML = "";

  list
    .sort((a, b) => b.initiative - a.initiative)
    .forEach((entry, index) => {
      const card = document.createElement("div");
      card.className = `card ${index === 0 && !isDM ? "current" : ""}`;
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
  renderList("initiative-list", initiativeList);
}

// DM-Ansicht
function renderDMTable() {
  renderList("initiative-list", initiativeList, true);
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

function deleteEntry(id) {
  initiativeList = initiativeList.filter((item) => item.id !== id);
  sendMessage("delete", id);
  renderDMTable();
}

// WebSocket
const socket = new WebSocket("ws://localhost:8080");

socket.addEventListener("open", () =>
  console.log("Verbunden mit dem WebSocket-Server")
);

socket.addEventListener("message", (event) => {
  const data = JSON.parse(event.data);
  if (data.type === "update" || data.type === "init") {
    initiativeList = data.payload;

    if (document.getElementById("initiative-form")) {
      renderDMTable(); // DM-Seite
    } else if (document.getElementById("initiative-list")) {
      renderInitiative(); // Spieler-Seite
    }
  }
});

function sendMessage(type, payload) {
  socket.send(JSON.stringify({ type, payload }));
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
