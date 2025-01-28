const WebSocket = require("ws");
const wss = new WebSocket.Server({ port: 8080 });

let initiativeList = [];
let currentTurnIndex = 0; // Globale Turn-Verwaltung

wss.on("connection", (ws) => {
  console.log("Ein Client hat sich verbunden.");

  // Sende initiale Daten an den Client
  ws.send(
    JSON.stringify({
      type: "init",
      payload: initiativeList,
      turnIndex: currentTurnIndex,
    })
  );

  // Nachrichten von Clients empfangen
  ws.on("message", (message) => {
    const data = JSON.parse(message);

    switch (data.type) {
      case "update":
        initiativeList = data.payload; // Liste aktualisieren
        broadcast({ type: "update", payload: initiativeList });
        break;

      case "add":
        initiativeList.push(data.payload); // Eintrag hinzufügen
        broadcast({ type: "update", payload: initiativeList });
        break;

      case "delete":
        initiativeList = initiativeList.filter(
          (item) => item.id !== data.payload
        );
        broadcast({ type: "update", payload: initiativeList }); // Liste aktualisieren
        break;

      case "nextTurn":
        currentTurnIndex = data.payload; // Turn-Index aktualisieren
        broadcast({ type: "nextTurn", payload: currentTurnIndex }); // Broadcast an alle Clients
        break;

      default:
        console.log("Unbekannter Nachrichtentyp:", data.type);
    }
  });

  ws.on("close", () => {
    console.log("Ein Client hat die Verbindung getrennt.");
  });
});

// Funktion: Broadcast an alle Clients
function broadcast(data) {
  wss.clients.forEach((client) => {
    if (client.readyState === WebSocket.OPEN) {
      client.send(JSON.stringify(data));
    }
  });
}

console.log("WebSocket-Server läuft auf ws://localhost:8080");
