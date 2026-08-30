import React, { useEffect, useState } from "react";
import { Bar, Doughnut } from "react-chartjs-2";
import {
  ArcElement,
  BarElement,
  CategoryScale,
  Chart as ChartJS,
  Legend,
  LinearScale,
  Tooltip,
} from "chart.js";
import api from "../api/api";
import "./Stats.css";

ChartJS.register(CategoryScale, LinearScale, BarElement, ArcElement, Tooltip, Legend);

export default function StatsPage() {
  const [data, setData] = useState(null);
  const [loading, setLoading] = useState(true);
  const [err, setErr] = useState("");

  useEffect(() => {
    const load = async () => {
      try {
        setLoading(true);
        setErr("");
        const res = await api.get("/stats");
        setData(res.data);
      } catch {
        setErr("Statistika trenutno nije dostupna.");
      } finally {
        setLoading(false);
      }
    };

    load();
  }, []);

  const categories = data?.byCategory ?? [];
  const statuses = data?.byStatus ?? [];

  const barData = {
    labels: categories.map((c) => c.name),
    datasets: [
      {
        label: "Broj događaja",
        data: categories.map((c) => c.events),
        backgroundColor: "#3b82f6",
        borderRadius: 8,
      },
      {
        label: "Broj prijava",
        data: categories.map((c) => c.registrations),
        backgroundColor: "#10b981",
        borderRadius: 8,
      },
    ],
  };

  const doughnutData = {
    labels: statuses.map((s) => s.status),
    datasets: [
      {
        data: statuses.map((s) => s.events),
        backgroundColor: ["#3b82f6", "#f59e0b", "#ef4444", "#64748b"],
      },
    ],
  };

  return (
    <div className="stats-page">
      <div className="stats-header">
        <div>
          <h1>Statistika</h1>
          <p>Pregled događaja i prijava po kategoriji i statusu.</p>
        </div>
      </div>

      {loading && <div className="stats-card">Učitavanje...</div>}
      {err && <div className="stats-error">{err}</div>}

      {!loading && !err && data && (
        <>
          <div className="stats-totals">
            <div className="stats-total-card">
              <span>Ukupno događaja</span>
              <strong>{data.totals.events}</strong>
            </div>
            <div className="stats-total-card">
              <span>Aktivne prijave</span>
              <strong>{data.totals.registrations}</strong>
            </div>
          </div>

          <div className="stats-grid">
            <div className="stats-card">
              <h2>Po kategoriji</h2>
              {categories.length === 0 ? (
                <p className="stats-empty">Nema podataka za prikaz.</p>
              ) : (
                <Bar
                  data={barData}
                  options={{
                    responsive: true,
                    plugins: { legend: { position: "bottom" } },
                    scales: { y: { beginAtZero: true, ticks: { precision: 0 } } },
                  }}
                />
              )}
            </div>
            <div className="stats-card">
              <h2>Po statusu</h2>
              {statuses.length === 0 ? (
                <p className="stats-empty">Nema podataka za prikaz.</p>
              ) : (
                <Doughnut
                  data={doughnutData}
                  options={{
                    responsive: true,
                    plugins: { legend: { position: "bottom" } },
                  }}
                />
              )}
            </div>
          </div>
        </>
      )}
    </div>
  );
}
