import React, { useCallback, useEffect, useState } from "react";
import { useParams, useNavigate, Link } from "react-router-dom";
import api from "../api/api";
import EventLocationMap from "../components/EventLocationMap";
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

function nowForApi() {
  return new Date().toISOString().slice(0, 19).replace("T", " ");
}

function getStoredUser() {
  try {
    const raw = localStorage.getItem("user");
    return raw ? JSON.parse(raw) : null;
  } catch {
    return null;
  }
}

function participantName(p) {
  const u = p?.user;
  if (!u) return `Korisnik #${p?.idUser ?? "-"}`;
  const name = `${u.firstName ?? ""} ${u.lastName ?? ""}`.trim();
  return name || u.email || `Korisnik #${p.idUser}`;
}

function isActiveParticipation(p) {
  return String(p?.status ?? "").toUpperCase() === "REGISTERED";
}

export default function EventDetails() {
  const { id } = useParams();
  const navigate = useNavigate();

  const [event, setEvent] = useState(null);
  const [participants, setParticipants] = useState([]);
  const [myParticipation, setMyParticipation] = useState(null);
  const [loading, setLoading] = useState(true);
  const [actionLoading, setActionLoading] = useState(false);
  const [err, setErr] = useState("");
  const [info, setInfo] = useState("");

  const user = getStoredUser();
  const token = localStorage.getItem("token");
  const role = String(user?.role || "").toUpperCase();
  const isStaff =
    role === "ORGANIZATOR" || role === "ADMIN" || role === "ADMINISTRATOR";
  const isLoggedIn = Boolean(token && user);

  const loadEvent = useCallback(async () => {
    const res = await api.get(`/events/${id}`);
    const data = Array.isArray(res.data) ? res.data[0] : res.data;
    setEvent(data);
    return data;
  }, [id]);

  const loadParticipations = useCallback(async () => {
    if (!token) {
      setParticipants([]);
      setMyParticipation(null);
      return;
    }

    try {
      const res = await api.get("/event-participations", {
        params: { idEvent: id },
      });
      const list = Array.isArray(res.data) ? res.data : res.data?.data ?? [];
      setParticipants(list);

      const mine =
        list.find((p) => Number(p.idUser) === Number(user?.id)) ?? null;
      setMyParticipation(mine);
    } catch {
      setParticipants([]);
      setMyParticipation(null);
    }
  }, [id, token, user?.id]);

  useEffect(() => {
    const fetchOne = async () => {
      try {
        setLoading(true);
        setErr("");
        await loadEvent();
        await loadParticipations();
      } catch (e) {
        console.error("Greška pri učitavanju događaja:", e);
        setErr("Događaj nije pronađen ili je došlo do greške.");
      } finally {
        setLoading(false);
      }
    };

    fetchOne();
  }, [loadEvent, loadParticipations]);

  const registeredCount =
    event?.registeredCount ??
    participants.filter(isActiveParticipation).length;
  const capacity = event?.capacity ?? null;
  const isFull = capacity != null && registeredCount >= capacity;
  const eventStatus = String(event?.status ?? "").toUpperCase();
  const canRegister =
    isLoggedIn &&
    eventStatus === "ACTIVE" &&
    (!myParticipation || !isActiveParticipation(myParticipation));
  const canUnregister =
    isLoggedIn && myParticipation && isActiveParticipation(myParticipation);

  const handleRegister = async () => {
    if (!isLoggedIn) {
      navigate("/login");
      return;
    }

    setActionLoading(true);
    setErr("");
    setInfo("");

    try {
      if (myParticipation && !isActiveParticipation(myParticipation)) {
        await api.put(`/event-participations/${myParticipation.idParticipation}`, {
          status: "REGISTERED",
          cancelledAt: null,
        });
      } else {
        await api.post("/event-participations", {
          idEvent: Number(id),
          status: "REGISTERED",
          registeredAt: nowForApi(),
        });
      }
      setInfo("Uspešno ste prijavljeni na događaj.");
      await loadEvent();
      await loadParticipations();
    } catch (e) {
      const resp = e.response;
      if (resp?.status === 401) {
        setErr("Morate biti prijavljeni da biste se registrovali na događaj.");
      } else if (resp?.status === 422) {
        setErr(
          resp.data?.message ||
            "Prijava nije moguća (već ste prijavljeni ili je kapacitet popunjen)."
        );
      } else {
        setErr("Prijava na događaj nije uspela.");
      }
    } finally {
      setActionLoading(false);
    }
  };

  const handleUnregister = async () => {
    if (!myParticipation) return;

    setActionLoading(true);
    setErr("");
    setInfo("");

    try {
      await api.put(`/event-participations/${myParticipation.idParticipation}`, {
        status: "CANCELLED",
        cancelledAt: nowForApi(),
      });
      setInfo("Odjavili ste se sa događaja.");
      await loadEvent();
      await loadParticipations();
    } catch (e) {
      setErr("Odjava sa događaja nije uspela.");
    } finally {
      setActionLoading(false);
    }
  };

  const handleDelete = async () => {
    const ok = window.confirm(
      "Da li ste sigurni da želite da obrišete ovaj događaj?"
    );
    if (!ok) return;

    setActionLoading(true);
    setErr("");
    setInfo("");

    try {
      await api.delete(`/events/${id}`);
      navigate("/events");
    } catch (e) {
      const status = e.response?.status;
      if (status === 401 || status === 403) {
        setErr("Nemate dozvolu da obrišete ovaj događaj.");
      } else {
        setErr("Brisanje događaja nije uspelo.");
      }
      setActionLoading(false);
    }
  };

  if (loading) {
    return <div className="event-details-page">Učitavanje...</div>;
  }

  if (err && !event) {
    return (
      <div className="event-details-page">
        <div className="error">{err || "Događaj nije pronađen."}</div>
        <button className="btn btn-secondary" onClick={() => navigate("/events")}>
          Nazad na listu
        </button>
      </div>
    );
  }

  if (!event) {
    return (
      <div className="event-details-page">
        <div className="error">Događaj nije pronađen.</div>
        <button className="btn btn-secondary" onClick={() => navigate("/events")}>
          Nazad na listu
        </button>
      </div>
    );
  }

  const categoryLabel = event.category?.name ?? event.idCategory ?? "-";

  return (
    <div className="event-details-page">
      <div className="event-details-card">
        <h1>{event.title}</h1>
        <p className={`status ${eventStatus.toLowerCase()}`}>{event.status}</p>

        {event.description && (
          <p className="description">{event.description}</p>
        )}

        {info && <div className="success">{info}</div>}
        {err && <div className="error">{err}</div>}

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
            <span className="v">
              {capacity != null ? `${registeredCount} / ${capacity}` : "-"}
            </span>
          </div>
          <div>
            <span className="k">Kategorija:</span>{" "}
            <span className="v">{categoryLabel}</span>
          </div>
        </div>

        <EventLocationMap location={event.location} title={event.title} />

        <div className="actions">
          <button
            type="button"
            className="btn btn-ghost"
            onClick={() => navigate("/events")}
          >
            Nazad na događaje
          </button>

          {!isLoggedIn && eventStatus === "ACTIVE" && (
            <Link className="btn btn-primary" to="/login">
              Prijavi se za učešće
            </Link>
          )}

          {canRegister && (
            <button
              type="button"
              className="btn btn-primary"
              onClick={handleRegister}
              disabled={actionLoading || isFull}
            >
              {isFull
                ? "Nema slobodnih mesta"
                : actionLoading
                  ? "Prijava..."
                  : "Prijavi se"}
            </button>
          )}

          {canUnregister && (
            <button
              type="button"
              className="btn btn-ghost"
              onClick={handleUnregister}
              disabled={actionLoading}
            >
              {actionLoading ? "Odjava..." : "Odjavi se"}
            </button>
          )}

          {isStaff && (
            <>
              <Link className="btn btn-secondary" to={`/events/${id}/edit`}>
                Izmeni
              </Link>
              <button
                type="button"
                className="btn btn-danger"
                onClick={handleDelete}
                disabled={actionLoading}
              >
                Obriši
              </button>
            </>
          )}
        </div>

        {isStaff && (
          <div className="participants">
            <h2>Lista učesnika</h2>
            {participants.length === 0 ? (
              <p className="empty">Još nema prijava na ovaj događaj.</p>
            ) : (
              <table className="participants-table">
                <thead>
                  <tr>
                    <th>Ime</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Prijava</th>
                  </tr>
                </thead>
                <tbody>
                  {participants.map((p) => (
                    <tr key={p.idParticipation}>
                      <td>{participantName(p)}</td>
                      <td>{p.user?.email ?? "-"}</td>
                      <td>
                        <span
                          className={`pill ${String(p.status).toLowerCase()}`}
                        >
                          {p.status}
                        </span>
                      </td>
                      <td>{formatDT(p.registeredAt)}</td>
                    </tr>
                  ))}
                </tbody>
              </table>
            )}
          </div>
        )}
      </div>
    </div>
  );
}
