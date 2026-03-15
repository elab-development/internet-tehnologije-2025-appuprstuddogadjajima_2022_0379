import React, { useEffect, useState } from "react";
import { useParams, useNavigate } from "react-router-dom";
import api from "../api/api";
import "./EventDetails.css";

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

export default function EventDetails() {
  const { id } = useParams();
  const navigate = useNavigate();

  const [event, setEvent] = useState(null);
  const [loading, setLoading] = useState(true);
  const [err, setErr] = useState("");

  useEffect(() => {
    const fetchOne = async () => {
      try {
        setLoading(true);
        setErr("");

        const res = await api.get(`/events/${id}`);
        setEvent(res.data);
      } catch (e) {
        console.error("Greška pri učitavanju događaja:", e);
        setErr("Događaj nije pronađen ili je došlo do greške.");
      } finally {
        setLoading(false);
      }
    };

    fetchOne();
  }, [id]);

  if (loading) {
    return <div className="event-details-page">Učitavanje...</div>;
  }

  if (err || !event) {
    return (
      <div className="event-details-page">
        <div className="error">{err || "Događaj nije pronađen."}</div>
        <button className="btn btn-secondary" onClick={() => navigate("/events")}>
          Nazad na listu
        </button>
      </div>
    );
  }

  return (
    <div className="event-details-page">
      <div className="event-details-card">
        <h1>{event.title}</h1>
        <p className="status">{event.status}</p>

        {event.description && (
          <p className="description">{event.description}</p>
        )}

        <div className="info-grid">
          <div>
            <span className="k">Lokacija:</span>{" "}
            <span className="v">{event.location ?? "-"}</span>
          </div>
          <div>
            <span className="k">Početak:</span>{" "}
            <span className="v">{formatDT(event.startAt)}</span>
          </div>
          <div>
            <span className="k">Kraj:</span>{" "}
            <span className="v">{formatDT(event.endAt)}</span>
          </div>
          <div>
            <span className="k">Kapacitet:</span>{" "}
            <span className="v">{event.capacity ?? "-"}</span>
          </div>
          <div>
            <span className="k">Kategorija (ID):</span>{" "}
            <span className="v">{event.idCategory ?? "-"}</span>
          </div>
        </div>

        <div className="actions">
          <button
            type="button"
            className="btn btn-secondary"
            onClick={() => navigate("/events")}
          >
            Nazad na događaje
          </button>
        </div>
      </div>
    </div>
  );
}
