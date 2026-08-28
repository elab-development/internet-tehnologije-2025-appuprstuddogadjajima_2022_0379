import React, { useEffect, useMemo, useRef, useState } from "react";
import { Link } from "react-router-dom";
import api from "../api/api";
import "./Calendar.css";

const WEEKDAYS = ["Pon", "Uto", "Sre", "Čet", "Pet", "Sub", "Ned"];

function pad(n) {
  return String(n).padStart(2, "0");
}

function dateKeyFromParts(year, monthIndex, day) {
  return `${year}-${pad(monthIndex + 1)}-${pad(day)}`;
}

function dateKeyFromDate(value) {
  if (!value) return "";
  const d = new Date(value);
  if (Number.isNaN(d.getTime())) return "";
  return dateKeyFromParts(d.getFullYear(), d.getMonth(), d.getDate());
}

function startOfDay(value) {
  const d = new Date(value);
  return new Date(d.getFullYear(), d.getMonth(), d.getDate());
}

function endOfDay(value) {
  const d = startOfDay(value);
  d.setHours(23, 59, 59, 999);
  return d;
}

function eventOverlapsDay(ev, day) {
  const start = ev?.startAt ? new Date(ev.startAt) : null;
  if (!start || Number.isNaN(start.getTime())) return false;
  const end = ev?.endAt ? new Date(ev.endAt) : start;
  const dayStart = startOfDay(day);
  const dayEnd = endOfDay(day);
  return start <= dayEnd && end >= dayStart;
}

function eventOverlapsMonth(ev, year, monthIndex) {
  const monthStart = new Date(year, monthIndex, 1);
  const monthEnd = new Date(year, monthIndex + 1, 0, 23, 59, 59, 999);
  const start = ev?.startAt ? new Date(ev.startAt) : null;
  if (!start || Number.isNaN(start.getTime())) return false;
  const end = ev?.endAt ? new Date(ev.endAt) : start;
  return start <= monthEnd && end >= monthStart;
}

function formatTime(dt) {
  if (!dt) return "";
  const d = new Date(dt);
  return d.toLocaleTimeString("sr-RS", { hour: "2-digit", minute: "2-digit" });
}

function formatLongDate(d) {
  return d.toLocaleDateString("sr-RS", {
    weekday: "long",
    day: "numeric",
    month: "long",
    year: "numeric",
  });
}

function buildMonthCells(year, monthIndex) {
  const first = new Date(year, monthIndex, 1);
  const startOffset = (first.getDay() + 6) % 7;
  const daysInMonth = new Date(year, monthIndex + 1, 0).getDate();
  const prevMonthDays = new Date(year, monthIndex, 0).getDate();
  const cells = [];

  for (let i = 0; i < startOffset; i += 1) {
    const day = prevMonthDays - startOffset + i + 1;
    const date = new Date(year, monthIndex - 1, day);
    cells.push({
      key: dateKeyFromDate(date),
      date,
      day,
      inMonth: false,
    });
  }

  for (let day = 1; day <= daysInMonth; day += 1) {
    const date = new Date(year, monthIndex, day);
    cells.push({
      key: dateKeyFromParts(year, monthIndex, day),
      date,
      day,
      inMonth: true,
    });
  }

  while (cells.length % 7 !== 0) {
    const extra = cells.length - (startOffset + daysInMonth) + 1;
    const date = new Date(year, monthIndex + 1, extra);
    cells.push({
      key: dateKeyFromDate(date),
      date,
      day: extra,
      inMonth: false,
    });
  }

  return cells;
}

export default function CalendarPage() {
  const today = useMemo(() => new Date(), []);
  const todayKey = dateKeyFromDate(today);

  const [events, setEvents] = useState([]);
  const [categories, setCategories] = useState([]);
  const [loading, setLoading] = useState(true);
  const [err, setErr] = useState("");
  const [q, setQ] = useState("");
  const [status, setStatus] = useState("ALL");
  const [category, setCategory] = useState("ALL");
  const [cursor, setCursor] = useState(
    () => new Date(today.getFullYear(), today.getMonth(), 1)
  );
  const [selectedKey, setSelectedKey] = useState(todayKey);
  const autoJumped = useRef(false);

  const year = cursor.getFullYear();
  const monthIndex = cursor.getMonth();

  useEffect(() => {
    const load = async () => {
      try {
        setLoading(true);
        setErr("");
        const [eventsRes, categoriesRes] = await Promise.all([
          api.get("/events"),
          api.get("/categories"),
        ]);
        const eventList = Array.isArray(eventsRes.data)
          ? eventsRes.data
          : eventsRes.data?.data ?? [];
        const categoryList = Array.isArray(categoriesRes.data)
          ? categoriesRes.data
          : categoriesRes.data?.data ?? [];
        setEvents(eventList);
        setCategories(categoryList);
      } catch (e) {
        setErr("Greška pri učitavanju kalendara.");
      } finally {
        setLoading(false);
      }
    };

    load();
  }, []);

  const categoryOptions = useMemo(() => {
    return categories
      .map((c) => ({
        value: String(c.idCategory ?? c.id ?? ""),
        label: c.name ?? `Kategorija ${c.idCategory ?? c.id}`,
      }))
      .filter((c) => c.value)
      .sort((a, b) => Number(a.value) - Number(b.value));
  }, [categories]);

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

      return matchesQuery && matchesStatus && matchesCategory;
    });
  }, [events, q, status, category]);

  useEffect(() => {
    if (autoJumped.current || loading || filtered.length === 0) {
      return;
    }

    const hasThisMonth = filtered.some((ev) =>
      eventOverlapsMonth(ev, year, monthIndex)
    );
    if (hasThisMonth) {
      autoJumped.current = true;
      return;
    }

    const starts = filtered
      .map((ev) => new Date(ev.startAt))
      .filter((d) => !Number.isNaN(d.getTime()))
      .sort((a, b) => a - b);

    const upcoming =
      starts.find((d) => d >= startOfDay(today)) ?? starts[starts.length - 1];
    if (!upcoming) {
      return;
    }

    autoJumped.current = true;
    setCursor(new Date(upcoming.getFullYear(), upcoming.getMonth(), 1));
    setSelectedKey(dateKeyFromDate(upcoming));
  }, [filtered, loading, monthIndex, today, year]);

  const cells = useMemo(
    () => buildMonthCells(year, monthIndex),
    [year, monthIndex]
  );

  const eventsByDay = useMemo(() => {
    const map = new Map();
    cells.forEach((cell) => {
      const dayEvents = filtered.filter((ev) => eventOverlapsDay(ev, cell.date));
      map.set(cell.key, dayEvents);
    });
    return map;
  }, [cells, filtered]);

  const selectedDate = useMemo(() => {
    const match = cells.find((c) => c.key === selectedKey);
    return match?.date ?? startOfDay(today);
  }, [cells, selectedKey, today]);

  const selectedEvents = eventsByDay.get(selectedKey) ?? [];
  const monthLabel = cursor.toLocaleDateString("sr-RS", {
    month: "long",
    year: "numeric",
  });

  const goMonth = (delta) => {
    setCursor((prev) => new Date(prev.getFullYear(), prev.getMonth() + delta, 1));
  };

  const goToday = () => {
    setCursor(new Date(today.getFullYear(), today.getMonth(), 1));
    setSelectedKey(todayKey);
  };

  return (
    <div className="calendar-page">
      <div className="calendar-header">
        <div>
          <h1>Kalendar događaja</h1>
          <p>Mesečni pregled. Klikni dan da vidiš događaje.</p>
        </div>
        <Link className="btn btn-ghost" to="/events">
          Lista događaja
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
              <option value="DRAFT">DRAFT</option>
              <option value="FINISHED">FINISHED</option>
            </select>
          </div>
          <div className="field">
            <label>Kategorija</label>
            <select
              value={category}
              onChange={(e) => setCategory(e.target.value)}
            >
              <option value="ALL">Sve</option>
              {categoryOptions.map((opt) => (
                <option key={opt.value} value={opt.value}>
                  {opt.label}
                </option>
              ))}
            </select>
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
              }}
            >
              Reset filtera
            </button>
          </div>
        </div>
      </div>

      {loading && <div className="state">Učitavanje kalendara...</div>}
      {err && <div className="state state-error">{err}</div>}

      {!loading && !err && (
        <div className="calendar-layout">
          <div className="calendar-card">
            <div className="month-bar">
              <button type="button" className="btn btn-ghost" onClick={() => goMonth(-1)}>
                Prethodni
              </button>
              <div className="month-title">{monthLabel}</div>
              <div className="month-nav-right">
                <button type="button" className="btn btn-ghost" onClick={goToday}>
                  Danas
                </button>
                <button type="button" className="btn btn-ghost" onClick={() => goMonth(1)}>
                  Sledeći
                </button>
              </div>
            </div>

            <div className="weekday-row">
              {WEEKDAYS.map((d) => (
                <div key={d} className="weekday">
                  {d}
                </div>
              ))}
            </div>

            <div className="days-grid">
              {cells.map((cell) => {
                const dayEvents = eventsByDay.get(cell.key) ?? [];
                const isSelected = cell.key === selectedKey;
                const isToday = cell.key === todayKey;
                const titles = dayEvents.slice(0, 2);

                return (
                  <button
                    key={cell.key}
                    type="button"
                    className={[
                      "day-cell",
                      cell.inMonth ? "" : "outside",
                      isSelected ? "selected" : "",
                      isToday ? "today" : "",
                      dayEvents.length ? "has-events" : "",
                    ]
                      .filter(Boolean)
                      .join(" ")}
                    onClick={() => setSelectedKey(cell.key)}
                  >
                    <div className="day-num">{cell.day}</div>
                    <div className="day-events">
                      {titles.map((ev) => (
                        <div
                          key={ev.idEvent ?? ev.id}
                          className={`chip ${String(ev.status).toLowerCase()}`}
                        >
                          {ev.title}
                        </div>
                      ))}
                      {dayEvents.length > 2 && (
                        <div className="more">+{dayEvents.length - 2}</div>
                      )}
                    </div>
                  </button>
                );
              })}
            </div>
          </div>

          <aside className="day-panel">
            <h2>{formatLongDate(selectedDate)}</h2>
            <p className="panel-meta">
              {selectedEvents.length === 0
                ? "Nema događaja ovog dana."
                : `${selectedEvents.length} događaj(a)`}
            </p>

            <div className="day-list">
              {selectedEvents.map((ev) => {
                const id = ev.idEvent ?? ev.id;
                return (
                  <div className="day-event" key={id}>
                    <div className="day-event-top">
                      <div className="title">{ev.title}</div>
                      <span className={`badge ${String(ev.status).toLowerCase()}`}>
                        {ev.status}
                      </span>
                    </div>
                    <div className="muted">
                      {formatTime(ev.startAt)}
                      {ev.endAt ? ` – ${formatTime(ev.endAt)}` : ""}
                      {ev.location ? ` · ${ev.location}` : ""}
                    </div>
                    <Link className="btn btn-secondary" to={`/events/${id}`}>
                      Detalji
                    </Link>
                  </div>
                );
              })}
            </div>
          </aside>
        </div>
      )}
    </div>
  );
}
