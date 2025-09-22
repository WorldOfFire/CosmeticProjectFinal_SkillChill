<?php
    session_start();

    if (empty($_SESSION['user'])) {
        header("Location: sign_in.php");
        exit;
    }

    $user_access = $_SESSION['access'];
    $user_first_login = $_SESSION['firstLogin'];
    $error = $_SESSION['error'] ?? '';
    unset($_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="pl">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>Portal B2B</title>
        <meta name="description" content="Portal B2B dla pracowników firmy kosmetycznej - dostęp do analiz i danych wewnętrznych.">
        <meta name="author" content="Firma Kosmetyczna">
        <meta name="robots" content="noindex, nofollow">

        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta http-equiv="Cache-Control" content="no-store">
        <meta http-equiv="Pragma" content="no-cache">
        <meta http-equiv="Expires" content="0">

        <meta name="theme-color" content="#d6336c">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

        <link rel="icon" href="favicon.ico" type="image/x-icon">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <script src="https://unpkg.com/react@18/umd/react.development.js" crossorigin></script>
        <script src="https://unpkg.com/react-dom@18/umd/react-dom.development.js" crossorigin></script>
        <script src="https://unpkg.com/@babel/standalone/babel.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <link rel="stylesheet" href="styleForms.css?v=<?php echo filemtime('styleForms.css'); ?>">
        <link rel="stylesheet" href="stylePosition.css?v=<?php echo filemtime('stylePosition.css'); ?>">
        <script src="activity_tracker.js"></script>
    </head>
    <body>
        <div class="triangle-right"></div>
        <header>
            <div class="logo-container"><img src="img/logo.png" alt="Logo"></div>
            <div class="header-text">
                <h1>Portal B2B dla dystrybutorów</h1>
                <h2>Firma kosmetyczna</h2>
            </div>
        </header>

        <main>
        <div class="main-container">
            <div class="return-container">
                <div>
                    <button onclick="window.location.href='main_panel.php?user=<?= urlencode($_SESSION['user']) ?>'"
                            type="button" id="return_button"
                            title="Powrót">
                        <i class="fa-solid fa-arrow-left"></i>
                    </button>
                </div>
                <div>
                    <button onclick="window.location.href='settings_panel.php?user=<?= urlencode($_SESSION['user']) ?>'" type="button" id="settings_button" title="Ustawienia konta">
                        <i class="fa-solid fa-gear"></i>
                    </button>
                    <button onclick="window.location.href='handler_logout.php'" type="button" id="logout_button" title="Wyloguj">
                        <i class="fa-solid fa-right-from-bracket"></i>
                    </button>
                </div>
            </div>
                <div class="action-container">
                <h2>Kwartalny Monitoring Zakupów i Sprzedaży</h2>
                <div id="quarter-container"></div>
            </div>
        </div>
        </main>

        <footer>
            <div>Skill&Chill - II etap rekrutacji - Szulińska Weronika</div>
        </footer>

        <script type="text/babel">
            function App() {
                const EXCHANGE_RATE = 4.5;
                const currentYear = new Date().getFullYear();
                const [quarter, setQuarter] = React.useState("1");
                const [year, setYear] = React.useState(currentYear);
                const [confirmed, setConfirmed] = React.useState(false);
                const [data, setData] = React.useState(null);

                const barChartRef = React.useRef(null);
                const lineChartRef = React.useRef(null);
                const pieChartRef = React.useRef(null);

                const fetchData = () => {
                    fetch(`handler_get_purchase.php?quarter=${quarter}&year=${year}`)
                        .then(res => res.json())
                        .then(json => {
                            const totalCurrentPln = parseFloat(json.totalCurrentPln || 0).toFixed(2);
                            const totalCurrentEur = (totalCurrentPln / EXCHANGE_RATE).toFixed(2);
                            const totalLastYearPln = parseFloat(json.totalLastYearPln || 0).toFixed(2);
                            const totalLastYearEur = (totalLastYearPln / EXCHANGE_RATE).toFixed(2);

                            setData({
                                lastYearSalesPl: totalLastYearPln,
                                lastYearSalesEur: totalLastYearEur,
                                purchasesPl: (0).toFixed(2),
                                purchasesEur: (0 / EXCHANGE_RATE).toFixed(2),
                                budgetPl: parseFloat(json.current?.budgetPl || 0).toFixed(2),
                                budgetEur: (parseFloat(json.current?.budgetPl || 0) / EXCHANGE_RATE).toFixed(2),
                                actualSalesPl: totalCurrentPln,
                                actualSalesEur: totalCurrentEur,
                                totalPOS: parseInt(json.current?.totalPOS || 0),
                                newOpenings: parseInt(json.current?.newOpenings || 0),
                                newOpeningsTarget: parseInt(json.current?.newOpeningsTarget || 0),
                                channels: json.channels?.map(c => ({
                                    ...c,
                                    sale_pln: parseFloat(c.sale_pln || 0).toFixed(2),
                                    sale_eur: (parseFloat(c.sale_pln || 0) / EXCHANGE_RATE).toFixed(2)
                                })) || []
                            });
                        })
                        .catch(err => console.error("Błąd pobierania danych:", err));
                    setConfirmed(true);
                };

                const cancelQuarter = () => {
                    setConfirmed(false);
                    setData(null);
                };

                const handlePlnChange = (field, integer = false) => (e) => {
                    let value = integer ? parseInt(e.target.value) || 0 : parseFloat(e.target.value) || 0;
                    if (value < 0) value = 0;
                    setData(prev => ({
                        ...prev,
                        [field]: integer ? value : value.toFixed(2),
                        ...(field.endsWith("Pl") && !integer ? { [field.replace("Pl", "Eur")]: (value / EXCHANGE_RATE).toFixed(2) } : {})
                    }));
                };

                const percentChange = (current, previous) => {
                    if (!previous || previous == 0) return "0%";
                    const percent = ((current - previous) / previous) * 100;
                    return percent.toFixed(2) + "%";
                };

                const submitData = () => {
                    if (!data) return;

                    const author = {
                        name_creator: "<?php echo $_SESSION['user_name']; ?>",
                        surname_creator: "<?php echo $_SESSION['user_surname']; ?>",
                        login_creator: "<?php echo $_SESSION['user']; ?>",
                        index_creator: "<?php echo $_SESSION['user_index']; ?>"
                    };

                    const payload = {
                        quarter,
                        year,
                        last_year_sales_pl: parseFloat(data.lastYearSalesPl),
                        last_year_sales_eur: parseFloat(data.lastYearSalesEur),
                        purchases_pl: parseFloat(data.purchasesPl),
                        purchases_eur: parseFloat(data.purchasesEur),
                        budget_pl: parseFloat(data.budgetPl),
                        budget_eur: parseFloat(data.budgetEur),
                        actual_sales_pl: parseFloat(data.actualSalesPl),
                        actual_sales_eur: parseFloat(data.actualSalesEur),
                        total_pos: parseInt(data.totalPOS),
                        new_openings: parseInt(data.newOpenings),
                        new_openings_target: parseInt(data.newOpeningsTarget),
                        ...author
                    };

                    fetch('handler_add_purchase.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(payload)
                    })
                    .then(res => res.json())
                    .then(response => {
                        if(response.success) {
                            fetch("handler_cvs_save_purchase.php", {
                                method: "POST",
                                headers: { "Content-Type": "application/json" },
                                body: JSON.stringify(payload)
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    // ✅ CSV zapisany, wyświetlenie okna potwierdzenia pobrania
                                    const confirmDownload = document.createElement("div");
                                    confirmDownload.style.position = "fixed";
                                    confirmDownload.style.top = "0";
                                    confirmDownload.style.left = "0";
                                    confirmDownload.style.width = "100%";
                                    confirmDownload.style.height = "100%";
                                    confirmDownload.style.backgroundColor = "rgba(0,0,0,0.5)";
                                    confirmDownload.style.display = "flex";
                                    confirmDownload.style.flexDirection = "column";
                                    confirmDownload.style.justifyContent = "center";
                                    confirmDownload.style.alignItems = "center";
                                    confirmDownload.style.zIndex = "9999";

                                    const box = document.createElement("div");
                                    box.style.backgroundColor = "#fff";
                                    box.style.padding = "30px";
                                    box.style.borderRadius = "10px";
                                    box.style.textAlign = "center";
                                    box.innerHTML = `
                                        <h2>Dane zapisane pomyślnie! Czy chcesz pobrać CSV?</h2>
                                        <div style="margin-top:20px;">
                                            <button id="csv-yes" style="margin-right:10px;padding:10px 20px;">Tak</button>
                                            <button id="csv-no" style="padding:10px 20px;">Nie</button>
                                        </div>
                                    `;

                                    confirmDownload.appendChild(box);
                                    document.body.appendChild(confirmDownload);

                                    document.getElementById("csv-no").onclick = () => {
                                        document.body.removeChild(confirmDownload);
                                    };

                                    document.getElementById("csv-yes").onclick = () => {
                                        const a = document.createElement("a");
                                        a.href = data.file_path;
                                        a.download = data.file_name;
                                        document.body.appendChild(a);
                                        a.click();
                                        document.body.removeChild(a);
                                        document.body.removeChild(confirmDownload);
                                        location.reload();
                                    };
                                } else {
                                    alert("❌ Błąd przy zapisie CSV: " + data.message);
                                }
                            })
                        } else {
                            alert("Błąd zapisu: " + response.error);
                        }
                    })
                    .catch(err => console.error(err));
                };

                React.useEffect(() => {
                    if (!data) return;

                    const barCtx = barChartRef.current.getContext("2d");
                    if (barCtx.chart) barCtx.chart.destroy();
                    barCtx.chart = new Chart(barCtx, {
                        type: "bar",
                        data: {
                            labels: data.channels.map(c => c.name || "Brak danych"),
                            datasets: [
                                { label: "Sprzedaż PLN", data: data.channels.map(c => parseFloat(c.sale_pln)), backgroundColor: "rgba(54, 162, 235, 0.5)" },
                                { label: "Sprzedaż EUR", data: data.channels.map(c => parseFloat(c.sale_eur)), backgroundColor: "rgba(255, 99, 132, 0.5)" }
                            ]
                        },
                        options: { responsive: false, plugins: { legend: { position: 'top' } }, maintainAspectRatio: false }
                    });

                    const lineCtx = lineChartRef.current.getContext("2d");
                    if (lineCtx.chart) lineCtx.chart.destroy();
                    lineCtx.chart = new Chart(lineCtx, {
                        type: "line",
                        data: {
                            labels: ["Zakupy bieżące", "Budżet"],
                            datasets: [{
                                label: "PLN",
                                data: [parseFloat(data.purchasesPl), parseFloat(data.budgetPl)],
                                borderColor: "rgba(75, 192, 192, 1)",
                                backgroundColor: "rgba(75, 192, 192, 0.2)",
                                tension: 0.4
                            }]
                        },
                        options: { responsive: false, plugins: { legend: { position: 'top' } }, maintainAspectRatio: false }
                    });

                    const pieCtx = pieChartRef.current.getContext("2d");
                    if (pieCtx.chart) pieCtx.chart.destroy();
                    pieCtx.chart = new Chart(pieCtx, {
                        type: "pie",
                        data: {
                            labels: data.channels.map(c => c.name || "Brak danych"),
                            datasets: [{
                                label: "Udział kanałów w sprzedaży",
                                data: data.channels.map(c => parseFloat(c.sale_pln)),
                                backgroundColor: data.channels.map((_, i) => `hsl(${i * 50 % 360}, 70%, 60%)`)
                            }]
                        },
                        options: { responsive: false, plugins: { legend: { position: 'right' } }, maintainAspectRatio: false }
                    });

                }, [data]);

                return (
                    <div>
                        <div className="form-controls">
                            <label>Kwartał: </label>
                            <select value={quarter} onChange={e => setQuarter(e.target.value)} disabled={confirmed}>
                                <option value="1">Q1</option>
                                <option value="2">Q2</option>
                                <option value="3">Q3</option>
                                <option value="4">Q4</option>
                            </select>
                            <label>Rok: </label>
                            <input type="number" value={year} min="2000" max={currentYear} onChange={e => setYear(e.target.value)} disabled={confirmed} />
                            {!confirmed ?
                                <button onClick={fetchData}>Pobierz dane</button> :
                                <button onClick={cancelQuarter}>Anuluj</button>
                            }
                        </div>

                        {confirmed && data && (
                            <div id="quarter-container">
                                <div id="form-container">
                                    <div>
                                        <h3>Zeszłoroczna sprzedaż</h3>
                                        PLN: <input type="number" value={data.lastYearSalesPl} disabled /><br/>
                                        EUR: <input type="number" value={data.lastYearSalesEur} disabled />
                                    </div>
                                    <div>
                                        <h3>Zakupy</h3>
                                        PLN: <input type="number" value={data.purchasesPl} onChange={handlePlnChange("purchasesPl")} /><br/>
                                        EUR: <input type="number" value={data.purchasesEur} disabled />
                                    </div>
                                    <div>
                                        <h3>Budżet</h3>
                                        PLN: <input type="number" value={data.budgetPl} onChange={handlePlnChange("budgetPl")} /><br/>
                                        EUR: <input type="number" value={data.budgetEur} disabled />
                                    </div>
                                    <div>
                                        <h3>Sprzedaż w kanałach</h3>
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th>Kanał</th>
                                                    <th>PLN</th>
                                                    <th>EUR</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {data.channels.length > 0 ? data.channels.map(c => (
                                                    <tr key={c.id}>
                                                        <td>{c.name || "Brak danych"}</td>
                                                        <td>{c.sale_pln}</td>
                                                        <td>{c.sale_eur}</td>
                                                    </tr>
                                                )) : <tr><td colSpan="3">Brak danych</td></tr>}
                                            </tbody>
                                        </table>
                                    </div>
                                    <div>
                                        <h3>Wskaźniki efektywności</h3>
                                        Wzrost/Spadek względem ubiegłego roku: {percentChange(data.purchasesPl, data.lastYearSalesPl)}<br/>
                                        Stopień wykonania planu budżetowego: {percentChange(data.purchasesPl, data.budgetPl)}
                                    </div>
                                    <div>
                                        <h3>Punkty sprzedaży</h3>
                                        Ogólna liczba działających gabinetów: <input type="number" value={data.totalPOS} onChange={handlePlnChange("totalPOS", true)} /><br/>
                                        Nowo otwarte: <input type="number" value={data.newOpenings} onChange={handlePlnChange("newOpenings", true)} /><br/>
                                        Plan na rok: <input type="number" value={data.newOpeningsTarget} onChange={handlePlnChange("newOpeningsTarget", true)} /><br/>
                                        Ile jeszcze do otwarcia: {Math.max((data.newOpeningsTarget || 0) - (data.newOpenings || 0), 0)}
                                    </div>
                                    <button onClick={submitData} className="submit-button">
                                        <i class="fa-solid fa-circle-check"></i>
                                    </button>
                                </div>

                                <div id="charts-container">
                                    <div className="chart-container">
                                        <h4>Sprzedaż w kanałach</h4>
                                        <canvas ref={barChartRef}></canvas>
                                    </div>
                                    <div className="chart-container">
                                        <h4>Zakupy vs Budżet</h4>
                                        <canvas ref={lineChartRef}></canvas>
                                    </div>
                                    <div className="chart-container">
                                        <h4>Udział kanałów w sprzedaży</h4>
                                        <canvas ref={pieChartRef}></canvas>
                                    </div>
                                </div>
                            </div>
                        )}

                        {confirmed && !data && <div>Ładowanie danych...</div>}
                    </div>
                );
            }

            ReactDOM.createRoot(document.getElementById("quarter-container")).render(<App />);
        </script>
    </body>
</html>
