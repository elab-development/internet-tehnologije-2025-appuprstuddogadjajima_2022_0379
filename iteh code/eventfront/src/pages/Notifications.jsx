import React, { useCallback, useEffect, useState } from "react";
import { Link, useNavigate } from "react-router-dom";
import api from "../api/api";
import "./Notifications.css";

function formatDT(dt) {
  if (!dt) return "-";
  return new Date(dt).toLocaleString("sr-RS", {
    year: "numeric",
    month: "2-digit",
    day: "2-digit",
    hour: "2-digit",
    minute: "2-digit",
  });
}

export default function Notifications() {
  const navigate = useNavigate();
  const [items, setItems] = useState([]);
  const [loading, setLoading] = useState(true);
  const [err, setErr] = useState("");

  const load = useCallback(async () => {
    const token = localStorage.getItem("token");
    if (!token) {
      navigate("/login");
      return;
    }

    try {
      setLoading(true);
      setErr("");
      const res = await api.get("/notifications");
      const list = Array.isArray(res.data) ? res.data : res.data?.data ?? [];
      setItems(list);
    } catch (e) {
      if (e.response?.status === 401) {
        navigate("/login");
        return;
      }
      setErr("Greška pri učitavanju obaveštenja.");
    } finally {
      setLoading(false);
    }
  }, [navigate]);

  useEffect(() => {
    load();
  }, [load]);

  const markSeen = async (id) => {
    try {
      await api.put(`/notifications/${id}`, { seen: true });
      setItems((prev) =>
        prev.map((n) =>
          n.idNotification === id ? { ...n, seen: true } : n
        )
      );
    } catch {
      setErr("Obaveštenje nije moglo da se označi kao pročitano.");
    }
  };

  const markAllSeen = async () => {
    const unseen = items.filter((n) => !n.seen);
    await Promise.all(
      unseen.map((n) => api.put(`/notifications/${n.idNotification}`, { seen: true }))
    );
    setItems((prev) => prev.map((n) => ({ ...n, seen: true })));
  };

  const remove = async (id) => {
    try {
      await api.delete(`/notifications/${id}`);
      setItems((prev) => prev.filter((n) => n.idNotification !== id));
    } catch {
      setErr("Brisanje obaveštenja nije uspelo.");
    }
  };

  const unseenCount = items.filter((n) => !n.seen).length;

  return (
    <div className="notifications-page">
      <div className="page-header">
        <div>
          <h1>Obaveštenja</h1>
          <p>Prijave, odjave i izmene događaja na koje si prijavljen.</p>
        </div>
        {unseenCount > 0 && (
          <button type="button" className="btn btn-ghost" onClick={markAllSeen}>
            Označi sve kao pročitano
          </button>
        )}
      </div>

      {loading && <div className="state">Učitavanje...</div>}
      {err && <div className="state state-error">{err}</div>}

      {!loading && !err && items.length === 0 && (
        <div className="state">Nemaš obaveštenja.</div>
      )}

      {!loading && !err && (
        <div className="note-list">
          {items.map((n) => {
            const eventId = n.event?.idEvent ?? n.idEvent;
            return (
              <div
                className={`note ${n.seen ? "seen" : "unseen"}`}
                key={n.idNotification}
              >
                <div className="note-top">
                  <span className={`pill ${String(n.type).toLowerCase()}`}>
                    {n.type}
                  </span>
                  <span className="when">{formatDT(n.created_at ?? n.createdAt)}</span>
                </div>
                <p className="message">{n.message}</p>
                {n.event?.title && (
                  <div className="muted">Događaj: {n.event.title}</div>
                )}
                <div className="note-actions">
                  {eventId && (
                    <Link className="btn btn-secondary" to={`/events/${eventId}`}>
                      Otvori događaj
                    </Link>
                  )}
                  {!n.seen && (
                    <button
                      type="button"
                      className="btn btn-ghost"
                      onClick={() => markSeen(n.idNotification)}
                    >
                      Pročitano
                    </button>
                  )}
                  <button
                    type="button"
                    className="btn btn-ghost"
                    onClick={() => remove(n.idNotification)}
                  >
                    Obriši
                  </button>
                </div>
              </div>
            );
          })}
        </div>
      )}
    </div>
  );
}
