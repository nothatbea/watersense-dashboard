document.addEventListener("DOMContentLoaded", () => {
    const API_URL = ".https://steelblue-skunk-833121.hostingersite.com/api/sensors/latest.php";

    const bar = document.getElementById("waterLevelBar");
    const valueText = document.getElementById("waterLevelValue");
    const locationText = document.getElementById("locationName");
    const statusIndicator = document.getElementById("statusIndicator");
    const banner = document.getElementById("statusBanner");
    const timeText = document.getElementById("currentTime");

    const MAX_CM = 150;
    const WATCH_CM = 90;
    const WARNING_CM = 120;

    async function fetchLatestWaterLevel() {
        try {
            const res = await fetch(API_URL);
            if (!res.ok) throw new Error("API not reachable");

            const data = await res.json();
            if (!Array.isArray(data) || !data.length) return;

            const row = data[0];
            let level = Number(row.water_level);

            // Clamp invalid values
            if (level < 0) level = 0;
            if (level > MAX_CM) level = MAX_CM;

            const percent = (level / MAX_CM) * 100;

            bar.style.height = percent + "%";
            valueText.textContent = `${level} cm`;
            locationText.textContent = row.location_name;
            timeText.textContent = new Date(row.created_at).toLocaleString();

            let status = "normal";
            if (level >= WARNING_CM) status = "warning";
            else if (level >= WATCH_CM) status = "caution";

            updateStatus(status, level);

        } catch (err) {
            console.error("Failed to fetch water level:", err.message);
        }
    }

    function updateStatus(status, level) {
        let label = "NORMAL";
        let indicatorClass = "status-normal";
        let bannerClass = "alert-success";
        let bannerText = "Water level is normal.";

        if (status === "caution") {
            label = "CAUTION";
            indicatorClass = "status-caution";
            bannerClass = "alert-warning";
            bannerText = `Water level rising (${level} cm). Stay alert.`;
        }

        if (status === "warning") {
            label = "WARNING";
            indicatorClass = "status-danger";
            bannerClass = "alert-error";
            bannerText = `CRITICAL: Water level ${level} cm. Prepare for evacuation.`;
        }

        statusIndicator.innerHTML = `
            <div class="status-indicator ${indicatorClass} status-pulse w-6 h-6"></div>
            <span class="text-xs font-medium uppercase">${label}</span>
        `;

        banner.className = `alert ${bannerClass}`;
        banner.querySelector("p").textContent = bannerText;
    }

    // Initial load + realtime polling
    fetchLatestWaterLevel();
    setInterval(fetchLatestWaterLevel, 5000);
});
