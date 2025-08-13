#include <WiFi.h>
#include <HTTPClient.h>
#include <ArduinoJson.h>

const char* ssid = "iPhone";
const char* password = "gelo123456";
const char* serverName = "http://172.20.10.2/CAPSTONEE/endpoint.php";

// User info (dynamically fetched)
int userID = 0;
String token = "";
bool userIDSet = false;
String username = "";

// Session management for dynamic user
String sessionCookie = "";
String sessionID = "";

// Sensor pins
const int SENSOR_PIN_1 = 34; //plastic bottle
const int SENSOR_PIN_2 = 35; //glass bottle
const int SENSOR_PIN_3 = 32; //tin cans

// Reading validation
const int NUM_READINGS = 10;
const int READING_DELAY = 50;

// Thresholds
const int PLASTIC_THRESHOLD_MIN = 100;
const int PLASTIC_THRESHOLD_MAX = 400;
const int BOTTLE_THRESHOLD_MIN = 500;
const int BOTTLE_THRESHOLD_MAX = 800;
const int CAN_THRESHOLD_MIN = 900;
const int CAN_THRESHOLD_MAX = 1200;
const int CONFIDENCE_THRESHOLD = 7;

unsigned long sensorPreviousMillis = 0;
const long sensorInterval = 2000;
const long readingInterval = 50;

// Get session ID from home.php
bool getSessionID() {
    if (WiFi.status() != WL_CONNECTED) {
        Serial.println("WiFi not connected. Cannot get session ID.");
        return false;
    }

    HTTPClient http;
    http.begin("http://172.20.10.2/CAPSTONEE/home.php");
    
    Serial.println("Getting session ID from home.php...");
    
    int httpResponseCode = http.GET();
    Serial.print("HTTP Response code: ");
    Serial.println(httpResponseCode);
    
    if (httpResponseCode > 0) {
        sessionID = http.getString();
        sessionID.trim(); // Remove any whitespace/newlines
        sessionCookie = "PHPSESSID=" + sessionID;
        
        Serial.print("Session ID: ");
        Serial.println(sessionID);
        Serial.print("Session Cookie: ");
        Serial.println(sessionCookie);
        
        http.end();
        return true;
    } else {
        Serial.print("Error getting session ID: ");
        Serial.println(httpResponseCode);
        Serial.print("Error details: ");
        Serial.println(http.errorToString(httpResponseCode));
    }
    http.end();
    return false;
}

// Fetch userID from the server using session
bool fetchUserFromServer() {
    if (WiFi.status() != WL_CONNECTED) {
        Serial.println("WiFi not connected. Cannot fetch userID.");
        return false;
    }

    HTTPClient http;
    http.begin(serverName);
    http.addHeader("Cookie", sessionCookie);

    Serial.println("Fetching current userID from the server...");

    int httpResponseCode = http.GET();
    Serial.print("HTTP Response code: ");
    Serial.println(httpResponseCode);

    if (httpResponseCode > 0) {
        String response = http.getString();
        Serial.println("Response: " + response);

        DynamicJsonDocument doc(1024);
        DeserializationError error = deserializeJson(doc, response);

        if (!error) {
            if (doc.containsKey("userID")) {
                userID = doc["userID"];
                username = doc["username"] | "Unknown";
                userIDSet = true;

                Serial.print("userID fetched successfully: ");
                Serial.println(userID);
                Serial.print("Username: ");
                Serial.println(username);
                http.end();
                return true;
            } else if (doc.containsKey("error")) {
                Serial.print("Server error: ");
                Serial.println(doc["error"].as<String>());
            }
        } else {
            Serial.print("JSON parsing failed: ");
            Serial.println(error.c_str());
        }
    } else {
        Serial.print("Error on HTTP GET: ");
        Serial.println(httpResponseCode);
        Serial.print("Error details: ");
        Serial.println(http.errorToString(httpResponseCode));
    }
    http.end();
    return false;
}

int getAverageReading(int sensorPin) {
    int total = 0;
    int validReadings = 0;

    for(int i = 0; i < NUM_READINGS; i++) {
        int reading = analogRead(sensorPin);
        if(reading > 0) {
            total += reading;
            validReadings++;
        }
        delay(readingInterval);
    }

    if(validReadings == 0) return 0;
    return total / validReadings;
}

String determineMaterial(int sensorValue) {
    Serial.print("Raw sensor value: ");
    Serial.println(sensorValue);

    int plasticCount = 0;
    int bottleCount = 0;
    int canCount = 0;

    int readings[NUM_READINGS];

    for(int i = 0; i < NUM_READINGS; i++) {
        readings[i] = analogRead(SENSOR_PIN_1); // Using primary sensor
        Serial.print("Reading ");
        Serial.print(i);
        Serial.print(": ");
        Serial.println(readings[i]);
        
        if(readings[i] >= PLASTIC_THRESHOLD_MIN && readings[i] <= PLASTIC_THRESHOLD_MAX) {
            plasticCount++;
        } else if(readings[i] >= BOTTLE_THRESHOLD_MIN && readings[i] <= BOTTLE_THRESHOLD_MAX) {
            bottleCount++;
        } else if(readings[i] >= CAN_THRESHOLD_MIN && readings[i] <= CAN_THRESHOLD_MAX) {
            canCount++;
        }
        delay(readingInterval);
    }

    Serial.print("Plastic count: ");
    Serial.println(plasticCount);
    Serial.print("Bottle count: ");
    Serial.println(bottleCount);
    Serial.print("Can count: ");
    Serial.println(canCount);

    if(plasticCount >= CONFIDENCE_THRESHOLD) {
        return "Plastic Bottles";
    } else if(bottleCount >= CONFIDENCE_THRESHOLD) {
        return "Glass Bottles";
    } else if(canCount >= CONFIDENCE_THRESHOLD) {
        return "Tin Can";
    }

    return "unknown";
}

void setup() {
    Serial.println("=== SETUP STARTING ===");
    Serial.begin(9600);
    delay(2000); // Wait for Serial to be ready
    Serial.println("Serial ready");

    Serial.println("ESP32 is starting...");
    Serial.println("Initializing WiFi...");

    WiFi.begin(ssid, password);
    Serial.print("Connecting to WiFi");

    int attempts = 0;
    while(WiFi.status() != WL_CONNECTED && attempts < 20) {
        delay(500);
        Serial.print(".");
        attempts++;
    }
    Serial.println();

    if(WiFi.status() == WL_CONNECTED) {
        Serial.print("Connected to SSID: ");
        Serial.println(WiFi.SSID());
        Serial.print("ESP32 IP Address: ");
        Serial.println(WiFi.localIP());
        
        // Get session ID first
        if (getSessionID()) {
            // Then fetch user info using the session
            fetchUserFromServer();
        }
    } else {
        Serial.println("Failed to connect to WiFi");
    }

    Serial.println("Calibrating sensor...");
    unsigned long startTime = millis();
    while(millis() - startTime < 1000) {
        // Wait for sensor to stabilize
    }

    int initialReading = getAverageReading(SENSOR_PIN_1);
    Serial.print("Initial sensor reading: ");
    Serial.println(initialReading);
}

void loop() {
    unsigned long currentMillis = millis();

    if(WiFi.status() != WL_CONNECTED) {
        Serial.println("WiFi Disconnected. Attempting to reconnect...");
        WiFi.begin(ssid, password);
        delay(5000);
        return;
    }

    // Check if it's time to read the sensor
    if(currentMillis - sensorPreviousMillis >= sensorInterval) {
        sensorPreviousMillis = currentMillis;

        // Get average sensor reading
        int sensorValue = getAverageReading(SENSOR_PIN_1);
        Serial.print("Average Sensor Value: ");
        Serial.println(sensorValue);

        // Determine material type with confidence
        String materialType = determineMaterial(sensorValue);
        Serial.print("Detected Material: ");
        Serial.println(materialType);

        if(materialType != "unknown") {
            HTTPClient http;
            
            // Debug server URL
            Serial.print("Connecting to server: ");
            Serial.println(serverName);
            
            http.begin(serverName);
            http.addHeader("Content-Type", "application/x-www-form-urlencoded");
            
            // Send sensor_value, material, userID, and token
            String httpRequestData = "sensor_value=" + String(sensorValue) + 
                                   "&material=" + materialType + 
                                   "&userID=" + String(userID) +
                                   "&token=" + token;
            
            Serial.print("Sending data: ");
            Serial.println(httpRequestData);
            
            // Debug HTTP request
            Serial.println("Sending POST request...");
            int httpResponseCode = http.POST(httpRequestData);
            Serial.print("HTTP Response code: ");
            Serial.println(httpResponseCode);
            
            if(httpResponseCode > 0) {
                String response = http.getString();
                Serial.println("Response: " + response);
                
                // Debug response details
                if(response.indexOf("Success") >= 0) {
                    Serial.println("Data successfully saved to database");
                } else if(response.indexOf("Error") >= 0) {
                    Serial.println("Error saving to database: " + response);
                }
            } else {
                Serial.print("Error on sending POST: ");
                Serial.println(httpResponseCode);
                Serial.print("Error details: ");
                Serial.println(http.errorToString(httpResponseCode));
            }
            
            http.end();
        } else {
            Serial.println("Material not identified with confidence");
        }
    }
} 