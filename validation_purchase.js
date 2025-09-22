document.addEventListener("DOMContentLoaded", () => {
  const rokInput = document.querySelector(".rok");
  const kwartalSelect = document.querySelector(".kwartal");

  const zakupyPoprzedniRok = document.querySelector(".zakupy-poprzedni-rok");
  const sprzedazFaktyczna = document.querySelector(".sprzedaz-faktyczna");
  const zakupy = document.querySelector(".zakupy");
  const budzet = document.querySelector(".budzet");
  const porownanieRok = document.querySelector(".porownanie-rok");
  const porownanieBudzet = document.querySelector(".porownanie-budzet");

  const lacznyPunkty = document.querySelector(".lacznypunktow");
  const nowePunkty = document.querySelector(".nowe-punkty");
  const planRoczny = document.querySelector(".plan-roczny");

  // Funkcja aktualizująca obliczenia
  function updateCalculations() {
    const lastSales = parseFloat(zakupyPoprzedniRok.value) || 0;
    const actualSales = parseFloat(sprzedazFaktyczna.value) || 0;
    const budgetValue = parseFloat(budzet.value) || 0;

    // YoY (%) = zmiana sprzedaży rok do roku
    porownanieRok.value = lastSales
      ? (((actualSales - lastSales) / lastSales) * 100).toFixed(2)
      : 0;

    // Wykonanie budżetu (%) = procent realizacji planu
    porownanieBudzet.value = budgetValue
      ? ((actualSales / budgetValue) * 100).toFixed(2)
      : 0;
  }

  // Funkcja pobierająca dane z serwera
  function fetchSalesData() {
    const quarter = parseInt(kwartalSelect.value);
    const year = parseInt(rokInput.value);

    fetch("handler_sales_fetch.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ quarter, year }),
    })
      .then((res) => res.json())
      .then((data) => {
        if (data.error) {
          alert(data.error);
          return;
        }

        zakupyPoprzedniRok.value = data.last_year_sales ?? 0;
        sprzedazFaktyczna.value = data.actual_sales ?? 0;
        zakupy.value = data.purchases ?? 0;
        budzet.value = data.budget ?? 0;
        lacznyPunkty.value = data.total_pos ?? 0;
        nowePunkty.value = data.new_openings ?? 0;
        planRoczny.value = data.new_openings_target ?? 0;

        updateCalculations();
      });
  }

  // Eventy
  kwartalSelect.addEventListener("change", fetchSalesData);
  rokInput.addEventListener("input", fetchSalesData);
  [zakupy, budzet].forEach((el) =>
    el.addEventListener("input", updateCalculations)
  );

  // Zapis raportu
  document.getElementById("btn-zapisz-raport").addEventListener("click", () => {
    const payload = {
      quarter: parseInt(kwartalSelect.value),
      year: parseInt(rokInput.value),
      report: [
        {
          last_year_sales: parseFloat(zakupyPoprzedniRok.value) || 0,
          purchases: parseFloat(zakupy.value) || 0,
          budget: parseFloat(budzet.value) || 0,
          actual_sales: parseFloat(sprzedazFaktyczna.value) || 0,
          yoy_comparison: parseFloat(porownanieRok.value) || 0,
          budget_comparison: parseFloat(porownanieBudzet.value) || 0,
          total_pos: parseInt(lacznyPunkty.value) || 0,
          new_openings: parseInt(nowePunkty.value) || 0,
          new_openings_target: parseInt(planRoczny.value) || 0,
        },
      ],
    };

    fetch("handler_sales_save.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(payload),
    })
      .then((res) => res.json())
      .then((data) => {
        if (data.error) alert(data.error);
        else alert("Raport zapisany poprawnie!");
      });
  });

  // Pobierz dane na start
  fetchSalesData();
});
