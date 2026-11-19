const tema = localStorage.getItem("tema") || "light";

document.documentElement.setAttribute("data-bs-theme", tema);

document.addEventListener("DOMContentLoaded", () => {
    const gumb = document.getElementById("gumbTema");

    function osveziGumb() {
        if (!gumb) return;

        const temna = document.documentElement.getAttribute("data-bs-theme") === "dark";

        gumb.textContent = temna ? "☀️ Svetli način" : "🌙 Temni način";
    }

    function posodobiBarveGrafov() {
        if (typeof Chart === "undefined") return;

        const temna = document.documentElement.getAttribute("data-bs-theme") === "dark";
        const barvaBesedila = temna ? "#dee2e6" : "#495057";
        const barvaMreze = temna
            ? "rgba(255,255,255,0.15)"
            : "rgba(0,0,0,0.1)";

        document.querySelectorAll('canvas[id^="grafVaja"]').forEach(canvas => {
            const graf = Chart.getChart(canvas);
            if (!graf) return;

            graf.options.plugins.legend.labels.color = barvaBesedila;

            ["x", "y"].forEach(os => {
                graf.options.scales[os].ticks.color = barvaBesedila;
                graf.options.scales[os].grid.color = barvaMreze;
            });

            graf.update("none");
        });
    }

    if (gumb) {
        gumb.addEventListener("click", () => {
            const trenutna = document.documentElement.getAttribute("data-bs-theme");
            const nova = trenutna === "dark" ? "light" : "dark";

            document.documentElement.setAttribute("data-bs-theme", nova);
            localStorage.setItem("tema", nova);

            osveziGumb();
            posodobiBarveGrafov();
        });
    }

    osveziGumb();
    posodobiBarveGrafov();
});