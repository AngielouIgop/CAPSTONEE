#include <WiFi.h>
#include <HTTPClient.h>

const char* ssid = "Montinola";
const char* password = "MONTinol@0815";
const char* serverName = "http://192.168.1.8/CAPSTONEE/endpoint.php";

//userID fetching
int userID = 0;
bool userIDSet = false;
String username = "";

const int SENSOR_PIN = 34;
const int NUM_READINGS = 10;
const int READING_DELAY = 50;

const int PLASTIC_THRESHOLD_MIN = 100;
const int PLASTIC_THRESHOLD_MAX = 600;
const int CONFIDENCE_THRESHOLD = 7;

unsigned long previousMillis = 0;
unsigned long sensorPreviousMillis = 0;
const long sensorInterval = 2000;
const long readingInterval = 50;

//Function to fetch userID from the server
// Function to fetch userID from the server
bool fetchUserFromServer() {
    if (WiFi.status() != WL_CONNECTED) { // 'WiFi', not 'Wifi'
        Serial.println("WiFi not connected. Cannot fetch userID.");
        return false;
    }

    HTTPClient http;
    http.begin("http://192.168.1.8/CAPSTONEE/endpoint.php");

    Serial.println("Fetching current userID from the server...");

    int httpResponseCode = http.GET();
    Serial.print("HTTP Response code: ");
    Serial.println(httpResponseCode); // Use println for new line

    if (httpResponseCode > 0) {
        String response = http.getString(); // '=' missing
        Serial.println("Response: " + response);

        DynamicJsonDocument doc(1024);
        DeserializationError error = deserializeJson(doc, response);

        if (!error) {
            if (doc.containsKey("userID")) {
                userID = doc["userID"]; // Use double quotes for JSON keys
                username = doc["username"] | "Unknown"; // Typo: "Unknown"
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

int getAverageReading() {
    int total = 0;
    int validReadings = 0;

    for(int i = 0; i < NUM_READINGS; i++) {
        int reading = analogRead(SENSOR_PIN);
        if(reading > 0) {
            total += reading;
            validReadings++;
        }

        unsigned long currentMillis = millis();
        while(millis() - currentMillis < readingInterval) {
            // Wait for reading interval
        }
    }

    if(validReadings == 0) return 0;
    return total / validReadings;
}

String determineMaterial(int sensorValue) {
    Serial.print("Raw sensor value: ");
    Serial.println(sensorValue);

    int plasticCount = 0;
    int readings[NUM_READINGS];

    for(int i = 0; i < NUM_READINGS; i++) {
        readings[i] = analogRead(SENSOR_PIN);
        Serial.print("Reading ");
        Serial.print(i);
        Serial.print(": ");
        Serial.println(readings[i]);
        
        if(readings[i] > PLASTIC_THRESHOLD_MIN && readings[i] < PLASTIC_THRESHOLD_MAX) {
            plasticCount++;
        }
        unsigned long currentMillis = millis();
        while(millis() - currentMillis < readingInterval) {
            // Wait for reading interval
        }
    }

    Serial.print("Plastic count: ");
    Serial.println(plasticCount);

    if(plasticCount >= CONFIDENCE_THRESHOLD) {
        return "Plastic Bottles";  // Must match exactly with database
    }

    return "unknown";
}

void setup() {
    Serial.begin(9600);
    while(!Serial) {
        ; // Wait for serial port to connect
    }

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
    } else {
        Serial.println("Failed to connect to WiFi");
    }

    Serial.println("Calibrating sensor...");
    unsigned long startTime = millis();
    while(millis() - startTime < 1000) {
        // Wait for sensor to stabilize
    }

    int initialReading = getAverageReading();
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
        int sensorValue = getAverageReading();
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
            
            // Send sensor_value, material, and userID
            String httpRequestData = "sensor_value=" + String(sensorValue) + 
                                   "&material=" + materialType + 
                                   "&userID=" + String(userID);
            
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