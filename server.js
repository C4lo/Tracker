const WebSocket = require("ws");
const wss = new WebSocket.Server({ port: 8080 });

let initiativeList = [];

wss.on("connection", (ws) => {
  console.log("Ein Client hat sich verbunden.");

  //send initial data to client
  ws.send(JSON.stringify({ type: "init", payload: initiativeList }));
  //get info from client
  ws.on("message", (message) => {
    const data = JSON.parse(message);

    switch (data.type) {
      case "update":
        initiativeList = data.payload; //refresh list
        broadcast({ type: "update", payload: initiativeList });
        break;

      case "add":
        initiativeList.push(data.payload); //add entry
        broadcast({ type: "update", payload: initiativeList });
        break;

      case "delete":
        initiativeList = initiativeList.filter(
          (item) => item.id !== data.payload
        );
        broadcast({ type: "update", payload: initiativeList }); //broadcast to all
        break;

      default:
        console.log("Unbekannter Nachrichtentyp:", data.type);
    }
  });

  ws.on("close", () => {
    console.log("Ein Client hat die Verbindung getrennt.");
  });
});

function broadcast(data) {
  wss.clients.forEach((client) => {
    if (client.readyState === WebSocket.OPEN) {
      client.send(JSON.stringify(data));
    }
  });
}

console.log("WebSocket-Server läuft auf ws://localhost:8080");
