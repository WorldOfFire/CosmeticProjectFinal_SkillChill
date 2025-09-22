function trackActivity() {
  fetch("track_activity.php")
    .then((response) => response.json())
    .then((data) => {
      if (data.status !== "success") {
        console.warn("Błąd śledzenia aktywności:", data);
      }
    })
    .catch((err) => console.error("Błąd AJAX:", err));
}

trackActivity();

setInterval(trackActivity, 30 * 60 * 1000);
