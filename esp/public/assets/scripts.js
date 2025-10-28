
window.onload = async function () {
    await init();
    document.getElementById('loading-message').style.display = 'none';
    document.getElementById('dashboard').style.display = 'block';
}

async function init() {
    // Chart inicializálása
    const canvas = document.getElementById('led-state-chart');
    window.chart = new Chart(canvas, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                data: [],
                tension: 0.1,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            animation: {
                duration: 0
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 1,
                    min: 0,
                    ticks: {
                        stepSize: 0.2,
                        callback: function (value) {
                            if (value > 1)
                                return '';
                            return `${value * 100}%`;
                        }
                    }
                }
            }
        }
    });
    // Kezdeti adatbetöltés
    await updateControls(true);
    await updateLed(true);
    await updateRGB(true);
    await updateChart(true);

    // A range változása esetén új adatbetöltés
    document.getElementById('range-select').addEventListener('change', async function () {
        updateControls();
        await updateChart(true);
    })

    // A frissítési idő változása esetén az intervallum újraindítása
    document.getElementById('refresh-select').addEventListener('change', async function () {
        clearInterval(window.refreshInterval);
        updateControls();
        await updateChart(true);
        const interval = parseInt(this.value) * 1000;
        if (interval > 0) {
            window.refreshInterval = setInterval(() => updateChart(), interval);
        }
    })

    // Alapértelmezett frissítési intervallum beállítása (2 másodperc)
    window.refreshInterval = setInterval(() => updateChart(), 2000);

    // Frissítés gomb eseménykezelője
    document.getElementById('refresh-button').addEventListener('click', function () {
        updateChart();
    })

    // Led kapcsoló gomb eseménykezelője
    document.querySelectorAll('input[name=led-state]').forEach(r => r.addEventListener('change', async function () {
        updateLed();
    }));

    // RGB kapcsoló gomb eseménykezelője
    document.querySelectorAll('input[name=rgb-state]').forEach(r => r.addEventListener('change', async function () {
        updateRGB();
    }));

    // RGB színválasztó eseménykezelője
    document.getElementById('rgb-color').addEventListener('input', async function () {
        updateRGB();
    });
}

async function updateChart(initial = false) {
    if (initial) {
        const range = document.getElementById('range-select').value;
        const refresh = document.getElementById('refresh-select').value;

        const response = await fetch('api/status.php?' + new URLSearchParams({range, refresh}));
        const json = await response.json();

        if (!response.ok) {
            console.error(json.error);
            return;
        }

        window.chart.data.labels = json.data.x;
        window.chart.data.datasets[0].data = json.data.y;
    } else {
        const response = await fetch('api/status.php?' + new URLSearchParams({type: 'last'}));
        const json = await response.json();

        if (!response.ok) {
            console.error(json.error);
            return;
        }

        window.chart.data.labels.shift();
        window.chart.data.datasets[0].data.shift();
        window.chart.data.labels.push(json.data.x);
        window.chart.data.datasets[0].data.push(json.data.y);
    }
    window.chart.update();
}

async function updateLed(initial = false) {
    const radio = document.querySelector('input[name=led-state]:checked');

    if (!initial) {
        const data = new FormData();
        data.append('state', radio.value);
        const response = await fetch('api/led.php', {
            method: 'POST',
            body: data
        });
        if (response.ok)
            return
        const json = await response.json();
        console.error(json.error);
    }

    const response = await fetch('api/led.php');
    const json = await response.json();

    if (!response.ok) {
        console.error(json.error);
        return;
    }

    document.querySelectorAll('input[name=led-state]').forEach(radio => {
        radio.checked = (radio.value === json.data.state);
    })
}

async function updateRGB(initial = false) {
    const radio = document.querySelector('input[name=rgb-state]:checked');
    const colorPicker = document.getElementById('rgb-color');

    if (!initial) {
        const data = new FormData();
        data.append('state', radio.value);
        data.append('red', parseInt(colorPicker.value.substring(1, 3), 16));
        data.append('green', parseInt(colorPicker.value.substring(3, 5), 16));
        data.append('blue', parseInt(colorPicker.value.substring(5, 7), 16));
        const response = await fetch('api/rgb.php', {
            method: 'POST',
            body: data
        });
        if (response.ok)
            return
        const json = await response.json();
        console.error(json.error);
    }

    const response = await fetch('api/rgb.php');
    const json = await response.json();

    if (!response.ok) {
        console.error(json.error);
        return;
    }

    document.querySelectorAll('input[name=rgb-state]').forEach(radio => {
        radio.checked = (radio.value === json.data.state);
    })

    colorPicker.value = "#" + Object.values(json.data.color)
        .map(x => x.toString(16).padStart(2, '0')).join('');
}

function updateControls(initial = false) {
    const range = document.getElementById('range-select');
    const refresh = document.getElementById('refresh-select');

    if (initial) {
        const controls = localStorage.getItem('controls');
        if (controls === undefined || controls === null)
            return;
        const json = JSON.parse(controls);
        if (json.hasOwnProperty('range'))
            range.value = json.range;
        if (json.hasOwnProperty('refresh'))
            refresh.value = json.refresh;
    } else {
        const json = JSON.stringify({
            range: range.value,
            refresh: refresh.value,
        });
        localStorage.setItem('controls', json);
    }
}