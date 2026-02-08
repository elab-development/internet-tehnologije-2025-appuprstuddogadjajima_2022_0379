import React, { useEffect, useMemo, useState } from "react";
import { Link } from "react-router-dom";
import "./Events.css";

const API_BASE = "http://localhost:8000"; // promeni ako treba

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

export default function EventsPage() {
  const [events, setEvents] = useState([]);
  const [loading, setLoading] = useState(true);
  const [err, setErr] = useState("");

  // filteri
  const [q, setQ] = useState("");
  const [status, setStatus] = useState("ALL");
  const [category, setCategory] = useState("ALL");
  const [dateFrom, setDateFrom] = useState("");
  const [dateTo, setDateTo] = useState("");

  useEffect(() => {
    const fetchEvents = async () => {
      try {
        setLoading(true);
        setErr("");

        const token = localStorage.getItem("token");

        const res = await fetch(`${API_BASE}/api/events`, {
          headers: {
            "Content-Type": "application/json",
            ...(token ? { Authorization: `Bearer ${token}` } : {}),
          },
        });

        if (!res.ok) {
          const txt = await res.text();
          throw new Error(`Greška (${res.status}): ${txt}`);
        }

        const data = await res.json();

        // podrška za: [..] ili { data: [..] }
        const list = Array.isArray(data) ? data : data?.data ?? [];
        setEvents(list);
      } catch (e) {
        setErr(e.message || "Greška pri učitavanju događaja.");
      } finally {
        setLoading(false);
      }
    };

    fetchEvents();
  }, []);

  const categoryOptions = useMemo(() => {
    const set = new Set();
    events.forEach((ev) => {
      if (ev?.idCategory != null) set.add(String(ev.idCategory));
    });
    return Array.from(set).sort((a, b) => Number(a) - Number(b));
  }, [events]);

  const filtered = useMemo(() => {
    const query = q.trim().toLowerCase();

    return events.filter((ev) => {
      const title = (ev?.title ?? "").toLowerCase();
      const loc = (ev?.location ?? "").toLowerCase();
      const desc = (ev?.description ?? "").toLowerCase();

      const matchesQuery =
        !query ||
        title.includes(query) ||
        loc.includes(query) ||
        desc.includes(query);

      const matchesStatus =
        status === "ALL" || String(ev?.status ?? "") === status;

      const matchesCategory =
        category === "ALL" || String(ev?.idCategory ?? "") === category;

      const start = ev?.startAt ? new Date(ev.startAt) : null;

      const matchesFrom = !dateFrom || (start && start >= new Date(dateFrom));
      const matchesTo = !dateTo || (start && start <= new Date(dateTo));

      return (
        matchesQuery &&
        matchesStatus &&
        matchesCategory &&
        matchesFrom &&
        matchesTo
      );
    });
  }, [events, q, status, category, dateFrom, dateTo]);

  return (
    <div className="events-page">
      <div className="events-header">
        <div>
          <h1>Događaji</h1>
          <p>Pregled aktuelnih događaja, pretraga i filtriranje.</p>
        </div>

        <Link className="btn btn-primary" to="/events/create">
          + Novi događaj
        </Link>
      </div>

      <div className="filters-card">
        <div className="filters-grid">
          <div className="field">
            <label>Pretraga</label>
            <input
              value={q}
              onChange={(e) => setQ(e.target.value)}
              placeholder="Naziv, lokacija, opis..."
            />
          </div>

          <div className="field">
            <label>Status</label>
            <select value={status} onChange={(e) => setStatus(e.target.value)}>
              <option value="ALL">Svi</option>
              <option value="ACTIVE">ACTIVE</option>
              <option value="CANCELLED">CANCELLED</option>
              <option value="FINISHED">FINISHED</option>
            </select>
          </div>

          <div className="field">
            <label>Kategorija (id)</label>
            <select
              value={category}
              onChange={(e) => setCategory(e.target.value)}
            >
              <option value="ALL">Sve</option>
              {categoryOptions.map((c) => (
                <option key={c} value={c}>
                  {c}
                </option>
              ))}
            </select>
          </div>

          <div className="field">
            <label>Od datuma</label>
            <input
              type="date"
              value={dateFrom}
              onChange={(e) => setDateFrom(e.target.value)}
            />
          </div>

          <div className="field">
            <label>Do datuma</label>
            <input
              type="date"
              value={dateTo}
              onChange={(e) => setDateTo(e.target.value)}
            />
          </div>

          <div className="field actions">
            <label>&nbsp;</label>
            <button
              className="btn btn-ghost"
              type="button"
              onClick={() => {
                setQ("");
                setStatus("ALL");
                setCategory("ALL");
                setDateFrom("");
                setDateTo("");
              }}
            >
              Reset filtera
            </button>
          </div>
        </div>
      </div>

      {loading && <div className="state">Učitavanje...</div>}
      {err && <div className="state state-error">{err}</div>}

      {!loading && !err && (
        <>
          <div className="meta">
            Prikazano: <b>{filtered.length}</b> / {events.length}
          </div>

          {filtered.length === 0 ? (
            <div className="state">Nema događaja za izabrane filtere.</div>
          ) : (
            <div className="events-grid">
              {filtered.map((ev) => {
                const id = ev.idEvent ?? ev.id ?? ev.eventId;

                return (
                  <div className="event-card" key={id}>
                    <div className="event-top">
                      <div className="title">{ev.title}</div>
                      <span
                        className={`badge ${String(ev.status).toLowerCase()}`}
                      >
                        {ev.status}
                      </span>
                    </div>

                    <div className="desc">
                      {ev.description ? ev.description : "—"}
                    </div>

                    <div className="info">
                      <div>
                        <span className="k">Lokacija:</span>{" "}
                        <span className="v">{ev.location ?? "-"}</span>
                      </div>
                      <div>
                        <span className="k">Početak:</span>{" "}
                        <span className="v">{formatDT(ev.startAt)}</span>
                      </div>
                      <div>
                        <span className="k">Kraj:</span>{" "}
                        <span className="v">{formatDT(ev.endAt)}</span>
                      </div>
                      <div>
                        <span className="k">Kapacitet:</span>{" "}
                        <span className="v">{ev.capacity ?? "-"}</span>
                      </div>
                      <div>
                        <span className="k">Kategorija:</span>{" "}
                        <span className="v">{ev.idCategory ?? "-"}</span>
                      </div>
                    </div>

                    <div className="card-actions">
                      <Link
                        className="btn btn-secondary"
                        to={`/events/${id}`}
                      >
                        Detalji
                      </Link>

                      <Link
                        className="btn btn-ghost"
                        to={`/events/${id}/edit`}
                      >
                        Izmeni
                      </Link>
                    </div>
                  </div>
                );
              })}
            </div>
          )}
        </>
      )}
    </div>
  );
}
