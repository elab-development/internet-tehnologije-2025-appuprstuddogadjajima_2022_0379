import React, { useEffect, useMemo, useState } from "react";
import { Link, useNavigate } from "react-router-dom";
import api from "../api/api";
import "./MyEvents.css";

function formatDT(dt) {
  if (!dt) return "-";
  const d = new Date(dt);
  return d.toLocaleString("sr-RS", {
    year: "numeric",
    month: "2-digit",
    day: "2-digit",
    hour: "2-digit",
    minute: "2-digit",
  });
}

export default function MyEvents() {
  const navigate = useNavigate();
  const [items, setItems] = useState([]);
  const [loading, setLoading] = useState(true);
  const [err, setErr] = useState("");
  const [filter, setFilter] = useState("REGISTERED");

  useEffect(() => {
    const token = localStorage.getItem("token");
    if (!token) {
      navigate("/login");
      return;
    }

    const load = async () => {
      try {
        setLoading(true);
        setErr("");
        const res = await api.get("/event-participations", {
          params: { mine: 1 },
        });
        const list = Array.isArray(res.data) ? res.data : res.data?.data ?? [];
        setItems(list);
      } catch (e) {
        if (e.response?.status === 401) {
          navigate("/login");
          return;
        }
        setErr("Greška pri učitavanju tvojih prijava.");
      } finally {
        setLoading(false);
      }
    };

    load();
  }, [navigate]);

  const filtered = useMemo(() => {
    const now = new Date();
    return items.filter((p) => {
      const status = String(p.status ?? "").toUpperCase();
      const end = p.event?.endAt ? new Date(p.event.endAt) : null;
      const isPast = end && end < now;

      if (filter === "REGISTERED") {
        return status === "REGISTERED";
      }
      if (filter === "CANCELLED") {
        return status === "CANCELLED";
      }
      if (filter === "HISTORY") {
        return status === "ATTENDED" || isPast || status === "CANCELLED";
      }
      return true;
    });
  }, [items, filter]);

  return (
    <div className="my-events-page">
      <div className="page-header">
        <div>
          <h1>Moje prijave</h1>
          <p>Događaji na koje si se prijavio i istorija odjava.</p>
        </div>
      </div>

      <div className="filters-card">
        <div className="tab-row">
          <button
            type="button"
            className={filter === "REGISTERED" ? "tab active" : "tab"}
            onClick={() => setFilter("REGISTERED")}
          >
            Aktivne
          </button>
          <button
            type="button"
            className={filter === "CANCELLED" ? "tab active" : "tab"}
            onClick={() => setFilter("CANCELLED")}
          >
            Odjavljene
          </button>
          <button
            type="button"
            className={filter === "HISTORY" ? "tab active" : "tab"}
            onClick={() => setFilter("HISTORY")}
          >
            Istorija
          </button>
          <button
            type="button"
            className={filter === "ALL" ? "tab active" : "tab"}
            onClick={() => setFilter("ALL")}
          >
            Sve
          </button>
        </div>
      </div>

      {loading && <div className="state">Učitavanje...</div>}
      {err && <div className="state state-error">{err}</div>}

      {!loading && !err && filtered.length === 0 && (
        <div className="state">Nema prijava za ovaj filter.</div>
      )}

      {!loading && !err && (
        <div className="cards">
          {filtered.map((p) => {
            const ev = p.event ?? {};
            const id = ev.idEvent ?? p.idEvent;
            return (
              <div className="card" key={p.idParticipation}>
                <div className="card-top">
                  <div className="title">{ev.title ?? `Događaj #${p.idEvent}`}</div>
                  <span className={`badge ${String(p.status).toLowerCase()}`}>
                    {p.status}
                  </span>
                </div>
                <div className="info">
                  <div>
                    <span className="k">Lokacija:</span> {ev.location ?? "-"}
                  </div>
                  <div>
                    <span className="k">Početak:</span> {formatDT(ev.startAt)}
                  </div>
                  <div>
                    <span className="k">Prijava:</span> {formatDT(p.registeredAt)}
                  </div>
                </div>
                {id && (
                  <Link className="btn btn-secondary" to={`/events/${id}`}>
                    Detalji
                  </Link>
                )}
              </div>
            );
          })}
        </div>
      )}
    </div>
  );
}
