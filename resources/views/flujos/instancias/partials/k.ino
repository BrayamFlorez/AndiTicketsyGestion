#include <WiFi.h>
#include <WiFiClientSecure.h>
#include <PubSubClient.h>
#include <ArduinoJson.h>

// ─── CONFIGURACIÓN DE RED Y HIVEMQ ───
const char* WIFI_SSID     = "RED SEGURA";
const char* WIFI_PASSWORD = "T0m4t32023*";

const char* MQTT_HOST     = "ba0d4a7f7fc1457a84592f87e73e4b1d.s1.eu.hivemq.cloud";
const int   MQTT_PORT     = 8883;
const char* MQTT_USER     = "rampedgym";              // Tu usuario de HiveMQ
const char* MQTT_PASS     = "Rampedgym2026";   // Tu contraseña de HiveMQ
const char* MQTT_TOPIC    = "ramped/gym/puerta";

// ─── CONFIGURACIÓN DEL HARDWARE ───
const int RELAY_PIN = 17; // CAMBIADO: Puerto GPIO 17
const unsigned long TIEMPO_APERTURA_MS = 2000; // CAMBIADO: 2 segundos (2000 ms)

// Ajusta estas constantes según el módulo de relé que estés usando:
// - Lógica invertida (La mayoría de relés): ACTIVAR = LOW, APAGAR = HIGH
// - Lógica directa: ACTIVAR = HIGH, APAGAR = LOW
const int RELAY_ACTIVAR = LOW;   
const int RELAY_APAGAR  = HIGH;  

// Variables para control del tiempo (sin bloquear con delay)
bool releActivo = false;
unsigned long tiempoInicio = 0;

WiFiClientSecure net;
PubSubClient client(net);

// ─── CALLBACK: Se ejecuta al recibir un mensaje MQTT ───
void callback(char* topic, byte* payload, unsigned int length) {
  Serial.print("Mensaje recibido en [");
  Serial.print(topic);
  Serial.print("]: ");

  StaticJsonDocument<256> doc;
  DeserializationError error = deserializeJson(doc, payload, length);

  if (error) {
    Serial.print("Error parseando JSON: ");
    Serial.println(error.f_str());
    return;
  }

  const char* action = doc["action"];
  int userId = doc["user_id"];

  Serial.printf("Acción: %s | Usuario ID: %d\n", action, userId);

  // Validar la orden de apertura
  if (action && strcmp(action, "UNLOCK") == 0) {
    Serial.println(">>> ¡APERTURA CONCEDIDA! Activando Relé en GPIO 17...");
    digitalWrite(RELAY_PIN, RELAY_ACTIVAR); // Activa el relé
    releActivo = true;
    tiempoInicio = millis();
  }
}

// ─── CONEXIÓN A WIFI Y HIVEMQ ───
void conectarWiFi() {
  Serial.print("Conectando a WiFi...");
  WiFi.begin(WIFI_SSID, WIFI_PASSWORD);
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println("\nWiFi Conectado! IP: " + WiFi.localIP().toString());
}

void reconectarMQTT() {
  while (!client.connected()) {
    Serial.print("Conectando a HiveMQ Cloud...");
    String clientId = "ESP32Relay-" + String(random(0xffff), HEX);
    
    if (client.connect(clientId.c_str(), MQTT_USER, MQTT_PASS)) {
      Serial.println("\n¡Conectado exitosamente a MQTT!");
      client.subscribe(MQTT_TOPIC);
      Serial.printf("Suscrito al topic: %s\n", MQTT_TOPIC);
    } else {
      Serial.print(" Falló conexión, rc=");
      Serial.print(client.state());
      Serial.println(" Retentando en 5 segundos...");
      delay(5000);
    }
  }
}

void setup() {
  Serial.begin(115200);
  
  pinMode(RELAY_PIN, OUTPUT);
  digitalWrite(RELAY_PIN, RELAY_APAGAR); // Mantiene el relé APAGADO por defecto al iniciar

  conectarWiFi();

  // Configuración SSL para HiveMQ Cloud
  net.setInsecure(); // Omitir verificación estricta de certificado CA en el ESP32
  client.setServer(MQTT_HOST, MQTT_PORT);
  client.setCallback(callback);
}

void loop() {
  // Mantener la conexión a MQTT activa
  if (!client.connected()) {
    reconectarMQTT();
  }
  client.loop();

  // Desactivar el relé automáticamente tras cumplir exactamente 2 segundos
  if (releActivo && (millis() - tiempoInicio >= TIEMPO_APERTURA_MS)) {
    digitalWrite(RELAY_PIN, RELAY_APAGAR); // Apaga el relé
    releActivo = false;
    Serial.println("<<< Tiempo cumplido. Relé Desactivado.");
  }
}