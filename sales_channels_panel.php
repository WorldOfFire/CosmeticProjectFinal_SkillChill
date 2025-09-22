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
            <script src="https://unpkg.com/react@18/umd/react.development.js" crossorigin></script>
            <script src="https://unpkg.com/react-dom@18/umd/react-dom.development.js" crossorigin></script>
            <script src="https://unpkg.com/@babel/standalone/babel.min.js"></script>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
            <link rel="stylesheet" href="styleForms.css?v=<?php echo filemtime('styleForms.css'); ?>">
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
                    <h2>Kwartalny Raport Sprzedażowy</h2>
                    <div id="quarter-container"></div>
                </div>
            </div>
        </main>
        <footer>Skill&Chill - II etap rekrutacji - Szulińska Weronika</footer>

        <script type="text/babel">
            const EUR_RATE = 4.5;
            
            function App() {
                const channels = ["Professional Sales","Pharmacy Sales","E-commerce Sales B2C","E-commerce Sales B2B","Third Party","Other"];
                const clientTypes = ["Osoba fizyczna","Przedsiębiorca","Spółka / Osoba prawna"];
                const currentYear = new Date().getFullYear();

                const [quarter, setQuarter] = React.useState("1");
                const [year, setYear] = React.useState(currentYear);
                const [quarterConfirmed, setQuarterConfirmed] = React.useState(false);

                const [activeChannel, setActiveChannel] = React.useState("");
                const [formData, setFormData] = React.useState({});
                const [clientsPerChannel, setClientsPerChannel] = React.useState({});
                const [productsPerChannel, setProductsPerChannel] = React.useState({});
                const [products, setProducts] = React.useState([]);
                const [inventoryData, setInventoryData] = React.useState([]);
                const [stockData, setStockData] = React.useState([]);


                React.useEffect(() => {
                    fetch("handler_products.php")
                        .then(res=>res.json())
                        .then(data=>setProducts(data))
                        .catch(err => console.error(err));
                }, []);

                // --- confirmQuarter ---
                const confirmQuarter = () => {
                    setQuarterConfirmed(true);
                    setActiveChannel(channels[0]);

                    const fd = {}; const cp = {}; const pp = {};
                    channels.forEach(ch => { fd[ch]={pln:"",eur:""}; cp[ch]=[]; pp[ch]=[]; });
                    setFormData(fd); setClientsPerChannel(cp); setProductsPerChannel(pp);

                    // --- DOMYŚLNE DANE MAGAZYNOWE (puste) ---
                    const defaultInventory = products.map(p => ({
                        sku: p.sku,
                        productName: p.name,
                        initialStock: 0,
                        delivery: 0,
                        soldQuantity: 0,
                        totalValue: 0
                    }));
                    setInventoryData(defaultInventory);
                };


                const cancelQuarter = () => {
                    setQuarterConfirmed(false);
                    setActiveChannel("");
                    setFormData({}); setClientsPerChannel({}); setProductsPerChannel({});
                };

                const handleImportFile = (event) => {
                    const file = event.target.files[0];
                    if (!file) return;

                    const formDataObj = new FormData();
                    formDataObj.append("excelFile", file);

                    fetch("handler_excel_sales.php", {
                        method: "POST",
                        body: formDataObj
                    })
                    .then(async res => {
                        const text = await res.text();
                        console.log("Odpowiedź PHP:", text);
                        return JSON.parse(text);
                    })
                    .then(data => {
                        if (!data.success) { 
                            alert("Błąd importu: " + data.message); 
                            return; 
                        }

                        setQuarter(data.quarter);
                        setYear(data.year);
                        confirmQuarter();

                        const fd = {}; // sprzedaż PLN/EUR
                        const cp = {}; // klienci
                        const pp = {}; // produkty

                        Object.entries(data.sheetsData).forEach(([sheetName, sheetData]) => {
                            // --- SPRZEDAŻ ---
                            let pln = 0, eur = 0;
                            const salesIndex = sheetData.findIndex(r => r[0]?.toString().toLowerCase() === "sprzedaż");
                            if(salesIndex !== -1 && sheetData[salesIndex + 2]){
                                const valuesRow = sheetData[salesIndex + 2];
                                pln = parseFloat((valuesRow[0] || 0).toString().replace(",", "."));
                                eur = parseFloat((valuesRow[1] || 0).toString().replace(",", "."));
                            }
                            fd[sheetName] = { pln, eur };

                            // --- KLIENCI ---
                            let clients = [];
                            const clientStart = sheetData.findIndex(r => r[0]?.toString().toLowerCase().includes("nowi klienci") || r[0]?.toString().toLowerCase().includes("klienci"));
                            if(clientStart !== -1) {
                                const headerRow = sheetData[clientStart + 1] || [];
                                for(let i = clientStart + 2; i < sheetData.length; i++) { // +2: pomijamy nagłówek
                                    const row = sheetData[i];
                                    if(!row || row.every(c => !c)) continue; // pomijamy puste wiersze
                                    // przerwij jeśli natrafimy na nagłówek produktów
                                    if(row[0]?.toString().toLowerCase().includes("produkty")) break;

                                    const type = row[0]?.toString().trim();
                                    if(!type) continue; // pomijamy wiersze bez typu klienta

                                    let clientData = {};
                                    if(type === "Osoba fizyczna") {
                                        clientData = {
                                            Imię: row[1] || "",
                                            Nazwisko: row[2] || "",
                                            Adres: row[8] || "",
                                            "tel.": row[9] || "",
                                            "e-mail": row[10] || ""
                                        };
                                    } else if(type === "Przedsiębiorca") {
                                        clientData = {
                                            Imię: row[1] || "",
                                            Nazwisko: row[2] || "",
                                            Firma: row[3] || "",
                                            NIP: row[5] || "",
                                            REGON: row[6] || "",
                                            Adres: row[8] || "",
                                            "tel.": row[9] || "",
                                            "e-mail": row[10] || ""
                                        };
                                    } else if(type === "Spółka / Osoba prawna") {
                                        clientData = {
                                            Firma: row[3] || "",
                                            "Rodzaj podmiotu": row[4] || "",
                                            NIP: row[5] || "",
                                            REGON: row[6] || "",
                                            KRS: row[7] || "",
                                            Adres: row[8] || "",
                                            "Osoba kontaktowa": row[11] || "",
                                            "Kontakt e-mail": row[12] || "",
                                            "Kontakt tel.": row[13] || ""
                                        };
                                    }

                                    clients.push({ type, data: clientData });
                                }
                            }
                            cp[sheetName] = clients;


                            // --- PRODUKTY ---
                            let products = [];
                            const productStart = sheetData.findIndex(r => r[0]?.toString().toLowerCase().includes("produkty"));
                            if (productStart !== -1) {
                                const headerRow = sheetData[productStart + 1] || [];
                                for (let i = productStart + 2; i < sheetData.length; i++) { // +2: pomijamy nagłówek
                                    const row = sheetData[i];
                                    if (!row || row.every(c => !c)) continue; // pomijamy puste wiersze
                                    // pomijamy wiersze nagłówków lub niepoprawne
                                    if (row[0]?.toString().toLowerCase().includes("miesiąc") && row[1]?.toString().toLowerCase().includes("sku")) continue;

                                    products.push({
                                        month: row[0] || "",
                                        sku: row[1] || "",
                                        productName: row[2] || "",
                                        quantity: parseInt(row[3] || 0),
                                        value: parseFloat((row[4] || 0).toString().replace(",", "."))
                                    });
                                }
                            }
                            pp[sheetName] = products;

                        });

                        setFormData(fd);
                        setClientsPerChannel(cp);
                        setProductsPerChannel(pp);

                        // --- INVENTORY / STAN MAGAZYNU ---
                        setInventoryData(data.inventoryData || []); 
                        console.log(data.inventoryData)

                        alert("Import zakończony sukcesem i dane wypełnione w formularzu!");
                    })
                    .catch(err => {
                        console.error("Błąd fetch/parsowania JSON:", err);
                        alert("Błąd importu pliku Excel!");
                    });
                };

                const handleSubmit = () => {
                    // 1. Sprawdzenie, czy jest przynajmniej jedna sprzedaż w PLN
                    const atLeastOneSale = Object.values(formData).some(
                        ch => parseFloat(ch.pln || 0) > 0
                    );
                    let formValid = atLeastOneSale;

                    // 2. Sprawdzenie wartości produktów vs sprzedaż w kanale (PLN)
                    Object.keys(productsPerChannel).forEach(ch => {
                        const channelPLN = parseFloat(formData[ch]?.pln || 0);
                        const totalProducts = (productsPerChannel[ch] || []).reduce((sum, p) => sum + p.value, 0);
                        if (channelPLN !== 0 && totalProducts !== channelPLN) formValid = false;
                    });

                    // 3. Walidacja magazynu
                    const hasStockError = stockData.some(p => p.initialStock + p.delivery < p.soldQuantity);
                    if (hasStockError) formValid = false;

                    // 4. Tworzenie payload
                    const payload = {
                        quarter,
                        year,
                        total_sales_pl: Object.values(formData).reduce((sum, ch) => sum + parseFloat(ch.pln || 0), 0),
                        channels: {},
                        inventory: stockData.map(p => ({
                            product_id: p.sku,
                            initial_stock: p.initialStock,
                            delivery: p.delivery,
                            sold_quantity: p.soldQuantity,
                            remaining: p.initialStock + p.delivery - p.soldQuantity
                        }))
                    };

                    // Mapowanie klientów na angielskie nazwy pól
                    Object.keys(formData).forEach(ch => {
                        const mappedClients = (clientsPerChannel[ch] || []).map(c => {
                            let newData = {};
                            if(c.type === "Osoba fizyczna"){
                                newData = {
                                    name: c.data["Imię"] || "",
                                    surname: c.data["Nazwisko"] || "",
                                    address: c.data["Adres"] || "",
                                    phone: c.data["tel."] || "",
                                    email: c.data["e-mail"] || ""
                                };
                                return { type: "individual", data: newData };
                            } else if(c.type === "Przedsiębiorca"){
                                newData = {
                                    name: c.data["Imię"] || "",
                                    surname: c.data["Nazwisko"] || "",
                                    company: c.data["Firma"] || "",
                                    nip: c.data["NIP"] || "",
                                    regon: c.data["REGON"] || "",
                                    address: c.data["Adres"] || "",
                                    phone: c.data["tel."] || "",
                                    email: c.data["e-mail"] || ""
                                };
                                return { type: "company", data: newData };
                            } else if(c.type === "Spółka / Osoba prawna"){
                                newData = {
                                    company: c.data["Firma"] || "",
                                    entity_type: c.data["Rodzaj podmiotu"] || "",
                                    nip: c.data["NIP"] || "",
                                    regon: c.data["REGON"] || "",
                                    krs: c.data["KRS"] || "",
                                    address: c.data["Adres"] || "",
                                    contact_person: c.data["Osoba kontaktowa"] || "",
                                    email: c.data["e-mail kontaktowy"] || "",
                                    phone: c.data["tel. kontaktowy"] || ""
                                };
                                return { type: "corporation", data: newData };
                            }
                            return c;
                        });

                        payload.channels[ch] = {
                            pln: parseFloat(formData[ch].pln || 0),
                            products: productsPerChannel[ch] || [],
                            clients: mappedClients
                        };
                    });

                    // 5. Wysyłka do PHP
                    if (formValid) {
                        console.log("📤 JSON wysyłany do PHP:", JSON.stringify(payload, null, 2));

                        fetch("handler_add_sales_channels.php", {
                            method: "POST",
                            headers: { "Content-Type": "application/json" },
                            body: JSON.stringify(payload)
                        })
                        .then(res => res.json())
                        .then(data => {
                            fetch("handler_cvs_save.php", {
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
                                    };
                                } else {
                                    alert("❌ Błąd przy zapisie CSV: " + data.message);
                                }
                            })
                        })
                        .catch(err => alert("❌ Błąd sieci/serwera!"));
                    } else {
                        alert("❌ Formularz zawiera błędy lub nie ma żadnej sprzedaży.");
                    }
                };

                const totalPLN = Object.values(formData).reduce((sum,ch)=>sum+parseFloat(ch.pln||0),0);
                const totalEUR = Object.values(formData).reduce((sum,ch)=>sum+parseFloat(ch.eur||0),0);
                const totalProductValue = Object.values(productsPerChannel).flat().reduce((sum,p)=>sum+p.value,0);

                return (
                    <div>
                        <div>
                            <button 
                                onClick={() => document.getElementById("importFile").click()}
                                style={{cursor:"pointer"}}
                                title="Importuj Excel">
                                <i className="fa-solid fa-file-import"></i>
                            </button>
                            <input
                                type="file"
                                id="importFile"
                                style={{display:"none"}}
                                accept=".xlsx,.xls"
                                onChange={handleImportFile}
                            />
                        </div>

                        <div>
                            <label>Kwartał:</label>
                            <select value={quarter} onChange={e=>setQuarter(e.target.value)} disabled={quarterConfirmed}>
                                <option value="1">Q1</option>
                                <option value="2">Q2</option>
                                <option value="3">Q3</option>
                                <option value="4">Q4</option>
                            </select>
                            <label>Rok:</label>
                            <input
                                type="number"
                                value={year}
                                onChange={e=>setYear(Math.min(e.target.value,currentYear))}
                                disabled={quarterConfirmed}
                                min="1900"
                                max={currentYear}
                            />
                            {!quarterConfirmed && <button onClick={confirmQuarter}>Zatwierdź kwartał</button>}
                            {quarterConfirmed && <button onClick={cancelQuarter}>Anuluj kwartał</button>}
                        </div>

                        {quarterConfirmed && (
                            <>
                                <SalesChannels
                                    channels={channels}
                                    clientTypes={clientTypes}
                                    activeChannel={activeChannel}
                                    setActiveChannel={setActiveChannel}
                                    formData={formData}
                                    setFormData={setFormData}
                                    clientsPerChannel={clientsPerChannel}
                                    setClientsPerChannel={setClientsPerChannel}
                                    productsPerChannel={productsPerChannel}
                                    setProductsPerChannel={setProductsPerChannel}
                                    products={products}
                                    quarter={quarter}
                                />

                                <div>
                                    <h3>Łączna sprzedaż we wszystkich kanałach</h3>
                                    <div>PLN: {totalPLN.toFixed(2)}</div>
                                    <div>EUR: {totalEUR.toFixed(2)}</div>
                                    <div>
                                        {totalProductValue.toFixed(2) === totalPLN.toFixed(2)
                                            ? <span style={{color:"green"}}>✅ Wartości produktów sprzedanych zgadzają się z sumą sprzedaży z wszystkich kanałów</span>
                                            : <span style={{color:"red"}}>❌ Wartości produktów nie zgadzają się z sumą sprzedaży ({totalProductValue.toFixed(2)} vs {totalPLN.toFixed(2)})</span>}
                                    </div>
                                </div>

                                <StockForm productsPerChannel={productsPerChannel} inventoryData={inventoryData} stockData={stockData} setStockData={setStockData}/>

                                <div>
                                    <button onClick={handleSubmit} title="Zatwierdź">
                                        <i className="fa-solid fa-circle-check"></i>
                                    </button>
                                </div>
                            </>
                        )}
                    </div>
                );
            }

            function SalesChannels({channels, clientTypes, activeChannel, setActiveChannel, formData, setFormData, clientsPerChannel, setClientsPerChannel, productsPerChannel, setProductsPerChannel, products, quarter}) {
                const [newClientType,setNewClientType] = React.useState(clientTypes[0]);
                const [newClientData,setNewClientData] = React.useState({});

                const handleInputChange = (field,value) => {
                    setFormData(prev=>{
                        const updated = {...prev,[activeChannel]:{...prev[activeChannel],[field]:value}};
                        if(field==="pln") updated[activeChannel].eur = value ? (parseFloat(value)/EUR_RATE).toFixed(2) : "";
                        if(field==="eur") updated[activeChannel].pln = value ? (parseFloat(value)*EUR_RATE).toFixed(2) : "";
                        return updated;
                    });
                };

                const changeChannel = (channel) => {
                    setActiveChannel(channel);
                    setNewClientData({});
                };

                const validateClientForm = () => {
                    let required = [];
                    if(newClientType==="Osoba fizyczna") required = ["Imię","Nazwisko","Adres","tel.","e-mail"];
                    else if(newClientType==="Przedsiębiorca") required = ["Imię","Nazwisko","Firma","NIP","REGON","Adres","tel.","e-mail"];
                    else required = ["Firma","Rodzaj podmiotu","NIP","REGON","KRS","Adres","Osoba kontaktowa","e-mail kontaktowy","tel. kontaktowy"];
                    return required.every(f => newClientData[f] && newClientData[f].trim()!=="");
                };

                const addClient = () => {
                    if(!validateClientForm()){ alert("Wypełnij wszystkie pola klienta!"); return; }
                    setClientsPerChannel(prev => ( {...prev,[activeChannel]: [...prev[activeChannel], {type: newClientType, data: newClientData}]} ));
                    setNewClientData({});
                };

                const removeClient = (i) => {
                    setClientsPerChannel(prev => ({...prev,[activeChannel]: prev[activeChannel].filter((_, idx) => idx !== i)}));
                };

                return (
                    <div>
                        <div>
                            {channels.map(ch => (
                                <button key={ch} onClick={() => changeChannel(ch)} style={{backgroundColor: activeChannel===ch ? "#f5b6b1" : "#fff", marginRight:"5px"}}>{ch}</button>
                            ))}
                        </div>

                        <div style={{marginTop:"20px"}}>
                            <h3>Sprzedaż w kanale</h3>
                            <label>PLN: </label>
                            <input type="number" value={formData[activeChannel]?.pln || ""} onChange={e => handleInputChange("pln", e.target.value)}/>
                            <label>EUR: </label>
                            <input type="number" value={formData[activeChannel]?.eur || ""} onChange={e => handleInputChange("eur", e.target.value)}/>
                        </div>

                        <SalesReportSKU 
                            activeChannel={activeChannel} 
                            products={products} 
                            productsPerChannel={productsPerChannel} 
                            setProductsPerChannel={setProductsPerChannel} 
                            channelPLN={parseFloat(formData[activeChannel]?.pln || 0)}
                            quarter={quarter}
                        />

                        <div style={{marginTop:"20px"}}>
                            <h3>Nowi klienci</h3>
                            <div id="label-new_clients">
                            {clientTypes.map(type => (
                                <label key={type} style={{marginRight:"10px"}}>
                                    <input type="radio" checked={newClientType===type} onChange={()=>setNewClientType(type)}/> {type}
                                </label>
                            ))}
                            </div>
                            <div id="data-clients">
                            {renderClientForm(newClientType,newClientData,setNewClientData)}
                            <button onClick={addClient} style={{marginLeft:"10px"}}>Dodaj klienta</button>
                            </div>
                            {clientsPerChannel[activeChannel]?.length > 0 &&
                                <table border="1" cellPadding="5" style={{marginTop:"10px"}}>
                                    <thead>
                                        <tr>
                                            <th>Typ</th>
                                            <th>Dane</th>
                                            <th>Usuń</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {clientsPerChannel[activeChannel].map((c, i) => (
                                            <tr key={i}>
                                                <td>{c.type}</td>
                                                <td>{Object.entries(c.data).map(([k,v]) => <div key={k}>{k}: {v}</div>)}</td>
                                                <td><button onClick={()=>removeClient(i)}>Usuń</button></td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            }
                        </div>
                    </div>
                );
            }

            function renderClientForm(type, data, setData) {
                const fields = {
                    "Osoba fizyczna":["Imię","Nazwisko","Adres","tel.","e-mail"],
                    "Przedsiębiorca":["Imię","Nazwisko","Firma","NIP","REGON","Adres","tel.","e-mail"],
                    "Spółka / Osoba prawna":["Firma","Rodzaj podmiotu","NIP","REGON","KRS","Adres","Osoba kontaktowa","e-mail kontaktowy","tel. kontaktowy"]
                };
                return fields[type].map(f => (
                    <div key={f}>{f}: <input value={data[f] || ""} onChange={e => setData({...data, [f]: e.target.value})}/></div>
                ));
            }

            function SalesReportSKU({ activeChannel, products, productsPerChannel, setProductsPerChannel, channelPLN, quarter }) {
                const [month, setMonth] = React.useState("");
                const [sku, setSku] = React.useState("");
                const [productName, setProductName] = React.useState("");
                const [quantity, setQuantity] = React.useState("");
                const [value, setValue] = React.useState("");
                const [availableMonths, setAvailableMonths] = React.useState([]);

                React.useEffect(() => {
                    const quarters = { "1": ["Styczeń","Luty","Marzec"], "2": ["Kwiecień","Maj","Czerwiec"], "3": ["Lipiec","Sierpień","Wrzesień"], "4": ["Październik","Listopad","Grudzień"] };
                    setAvailableMonths(quarters[quarter] || []);
                    setMonth("");
                }, [quarter, activeChannel]);

                React.useEffect(()=>{ setMonth(""); setSku(""); setProductName(""); setQuantity(""); setValue(""); }, [activeChannel]);

                React.useEffect(()=>{ if (sku) { const p = products.find(x => x.sku === sku); if(p) setProductName(p.name); } else setProductName(""); }, [sku, products]);
                React.useEffect(()=>{ if (productName) { const p = products.find(x => x.name === productName); if(p) setSku(p.sku); } else setSku(""); }, [productName, products]);

                const addProduct = () => {
                    if(!month || !sku || !productName || !quantity || !value) { alert("Uzupełnij wszystkie pola produktu."); return; }
                    const newItem = { month, sku, productName, quantity: parseInt(quantity), value: parseFloat(value) };
                    setProductsPerChannel(prev => {
                        const updated = [...(prev[activeChannel] || [])];
                        const idx = updated.findIndex(p => p.sku === sku && p.month === month);
                        if(idx !== -1){ updated[idx].quantity += newItem.quantity; updated[idx].value += newItem.value; } else updated.push(newItem);
                        return {...prev, [activeChannel]: updated};
                    });
                    setMonth(""); setSku(""); setProductName(""); setQuantity(""); setValue("");
                };

                const removeProduct = (i) => { setProductsPerChannel(prev => ({...prev, [activeChannel]: prev[activeChannel].filter((_, idx) => idx !== i)})); };

                const report = productsPerChannel[activeChannel] || [];
                const totalValue = report.reduce((sum,p) => sum+p.value, 0);

                return (
                    <div>
                        <h3>Lista produktów</h3>
                        <div><label>Miesiąc: </label><select value={month} onChange={e=>setMonth(e.target.value)}><option value="">-- wybierz --</option>{availableMonths.map(m=><option key={m} value={m}>{m}</option>)}</select></div>
                        <div>
                            <label>SKU: </label><select value={sku} onChange={e=>setSku(e.target.value)}><option value="">-- wybierz SKU --</option>{products.map(p=><option key={p.id_product} value={p.sku}>{p.sku}</option>)}</select>
                            <label>Nazwa produktu: </label><input type="text" list="productNames" value={productName} onChange={e=>setProductName(e.target.value)}/>
                            <datalist id="productNames">{products.map(p=><option key={p.id_product} value={p.name} />)}</datalist>
                        </div>
                        <div>
                            <label>Ilość: </label><input type="number" value={quantity} onChange={e=>setQuantity(e.target.value)}/>
                            <label>Wartość: </label><input type="number" value={value} onChange={e=>setValue(e.target.value)}/>
                        </div>
                        <button onClick={addProduct}>Dodaj produkt</button>

                        {report.length===0 ? <div></div> :
                        <table border="1" cellPadding="5">
                            <thead><tr><th>Miesiąc</th><th>SKU</th><th>Nazwa</th><th>Ilość</th><th>Wartość</th><th>Akcja</th></tr></thead>
                            <tbody>{report.map((p,i)=><tr key={i}><td>{p.month}</td><td>{p.sku}</td><td>{p.productName}</td><td>{p.quantity}</td><td>{p.value.toFixed(2)}</td><td><button onClick={()=>removeProduct(i)}>Usuń</button></td></tr>)}</tbody>
                        </table>}

                        <div>
                            <strong>Suma wartości produktów: {totalValue.toFixed(2)}</strong>
                            {totalValue === channelPLN ? <span style={{color:"green"}}> ✅ Zgadza się ze sprzedażą w kanale</span> : <span style={{color:"red"}}> ❌ Nie zgadza się ({channelPLN})</span>}
                        </div>
                    </div>
                );
            }

            // --- StockForm ---
            function StockForm({ productsPerChannel, inventoryData, stockData, setStockData }) {
                // --- synchronizacja stockData z produktami i magazynem ---
                React.useEffect(() => {
                    const manualProducts = Object.values(productsPerChannel).flat() || [];
                    const excelProducts = inventoryData || [];

                    const filteredManual = manualProducts.filter(p => p.quantity > 0 || p.value > 0);
                    const filteredExcel = excelProducts.filter(p => p.soldQuantity > 0 || p.totalValue > 0);

                    const allSKUs = Array.from(new Set([...filteredManual.map(p => p.sku), ...filteredExcel.map(p => p.sku)]));

                    const newStock = allSKUs.map(sku => {
                        const manualItem = filteredManual.find(p => p.sku === sku);
                        const excelItem = filteredExcel.find(p => p.sku === sku);

                        return {
                            sku,
                            productName: manualItem?.productName || excelItem?.productName || sku,
                            initialStock: excelItem?.initialStock || 0,
                            delivery: excelItem?.delivery || 0,
                            soldQuantity: manualItem?.quantity || excelItem?.soldQuantity || 0,
                            totalValue: manualItem?.value || excelItem?.totalValue || 0
                        };
                    });

                    setStockData(newStock);
                }, [productsPerChannel, inventoryData, setStockData]);

                const handleChange = (sku, field, value) => {
                    setStockData(prev => prev.map(p => p.sku === sku ? { ...p, [field]: parseInt(value) || 0 } : p));
                };

                const getRemaining = p => p.initialStock + p.delivery - p.soldQuantity;
                const hasError = stockData.some(p => getRemaining(p) < 0);

                if (stockData.length === 0) return null;

                return (
                    <div style={{ marginTop: "30px" }}>
                        <h3>Stan magazynu</h3>
                        <table border="1" cellPadding="5">
                            <thead>
                                <tr>
                                    <th>SKU</th>
                                    <th>Produkt</th>
                                    <th>Sprzedane sztuki</th>
                                    <th>Średnia wartość jednej sztuki</th>
                                    <th>Ilość początkowa</th>
                                    <th>Ilość z dostawy</th>
                                    <th>Aktualny stan</th>
                                </tr>
                            </thead>
                            <tbody>
                                {stockData.map(p => {
                                    const avgValue = p.soldQuantity ? (p.totalValue / p.soldQuantity).toFixed(2) : "0.00";
                                    const remaining = getRemaining(p);
                                    const inputStyle = remaining < 0 ? { backgroundColor: "#f8d7da" } : {};
                                    return (
                                        <tr key={p.sku}>
                                            <td>{p.sku}</td>
                                            <td>{p.productName}</td>
                                            <td>{p.soldQuantity}</td>
                                            <td>{avgValue}</td>
                                            <td>
                                                <input
                                                    type="number"
                                                    min="0"
                                                    value={p.initialStock}
                                                    onChange={e => handleChange(p.sku, 'initialStock', e.target.value)}
                                                    style={inputStyle}
                                                />
                                            </td>
                                            <td>
                                                <input
                                                    type="number"
                                                    min="0"
                                                    value={p.delivery}
                                                    onChange={e => handleChange(p.sku, 'delivery', e.target.value)}
                                                    style={inputStyle}
                                                />
                                            </td>
                                            <td>{remaining >= 0 ? remaining : 0}</td>
                                        </tr>
                                    );
                                })}
                            </tbody>
                        </table>
                        {hasError && <div style={{ color: "red", marginBottom: "10px" }}>❌ Sprzedano więcej produktów niż było w magazynie!</div>}
                    </div>
                );
            }

            ReactDOM.createRoot(document.getElementById("quarter-container")).render(<App />);
        </script>
    </body>
</html>
