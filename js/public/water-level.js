document.addEventListener("DOMContentLoaded", () => {
    const API_URL = ".https://steelblue-skunk-833121.hostingersite.com/api/sensors/latest.php";

// CONFIG (must match ESP32 / backend logic)
const MAX_HEIGHT_CM = 150;
const WATCH_LEVEL = 90;     // cm
const WARNING_LEVEL = 120;  // cm

async function loadWaterLevel() {
  try {
    const res = await fetch(API_URL);
    const data = await res.json();

    if (!Array.isArray(data) || data.length === 0) return;

    const sensor = data[0];

    const level = parseFloat(sensor.water_level);
    const location = sensor.location_name;
    const updatedAt = new Date(sensor.created_at.replace(" ", "T"));

    const percent = Math.min((level / MAX_HEIGHT_CM) * 100, 100);

    // ===== Update DOM =====
    document.getElementById("locationName").textContent = location;
    document.getElementById("waterLevelValue").textContent = `${level} cm`;

    document.getElementById("currentTime").textContent =
      updatedAt.toLocaleString();

    const bar = document.getElementById("waterLevelBar");
    bar.style.height = percent + "%";

    // ===== Status Logic =====
    let status = "normal";
    let bannerText = "Water level is within safe range.";
    let bannerClass = "alert-success";

    if (level >= WARNING_LEVEL) {
      status = "warning";
      bannerText = "⚠️ DANGER: Water level is critically high!";
      bannerClass = "alert-error";
    } else if (level >= WATCH_LEVEL) {
      status = "caution";
      bannerText = "Water level is approaching caution threshold.";
      bannerClass = "alert-warning";
    }

    // Update banner
    const banner = document.getElementById("statusBanner");
    banner.className = `alert ${bannerClass} animate-fade-in`;
    banner.querySelector("p:nth-child(2)").textContent = bannerText;

    // Update indicator label
    const indicator = document.getElementById("statusIndicator");
    indicator.querySelector("span").textContent = status.toUpperCase();

  } catch (err) {
    console.error("Failed to load water level", err);
  }
}

// Initial load + auto refresh
loadWaterLevel();
setInterval(loadWaterLevel, 5000);
