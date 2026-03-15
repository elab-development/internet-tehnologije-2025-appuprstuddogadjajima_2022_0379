import React, { useEffect, useState } from "react";
import { useNavigate } from "react-router-dom";
import api from "../api/api";
import "./CreateEvent.css";

export default function CreateEvent() {
  const navigate = useNavigate();

  const [title, setTitle] = useState("");
  const [description, setDescription] = useState("");
  const [location, setLocation] = useState("");
  const [startAt, setStartAt] = useState("");
  const [endAt, setEndAt] = useState("");
  const [capacity, setCapacity] = useState("");
  const [idCategory, setIdCategory] = useState("");
  const [status, setStatus] = useState("ACTIVE");

  const [categories, setCategories] = useState([]);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState("");
  const [fieldErrors, setFieldErrors] = useState({});

  useEffect(() => {
    const rawUser = localStorage.getItem("user");
    if (!rawUser) {
      navigate("/login");
      return;
    }

    const user = JSON.parse(rawUser);
    const role = String(user?.role || "").toUpperCase();
    const canCreate =
      role === "ORGANIZATOR" || role === "ADMIN" || role === "ADMINISTRATOR";

    if (!canCreate) {
      navigate("/events");
      return;
    }

    const fetchCategories = async () => {
      try {
        const res = await api.get("/categories");
        const data = Array.isArray(res.data) ? res.data : res.data?.data ?? [];
        setCategories(data);
      } catch (e) {
        console.error("Greška pri učitavanju kategorija:", e);
      }
    };

    fetchCategories();
  }, [navigate]);

  const handleSubmit = async (e) => {
    e.preventDefault();
    setLoading(true);
    setError("");
    setFieldErrors({});

    try {
      const rawUser = localStorage.getItem("user");
      if (!rawUser) {
        navigate("/login");
        return;
      }
      const user = JSON.parse(rawUser);

      const payload = {
        idUser: user.id,
        idCategory: idCategory ? Number(idCategory) : null,
        title,
        description,
        location,
        startAt,
        endAt,
        capacity: capacity ? Number(capacity) : null,
        status,
      };

      const res = await api.post("/events", payload);
      const created = Array.isArray(res.data) ? res.data[0] : res.data;

      const id = created?.idEvent ?? created?.id ?? null;

      if (id != null) {
        navigate(`/events/${id}`);
      } else {
        navigate("/events");
      }
    } catch (err) {
      console.error("Create event error:", err);
      const resp = err.response;

      if (resp?.status === 422 && resp.data?.errors) {
        setFieldErrors(resp.data.errors);
      } else if (resp?.status === 401 || resp?.status === 403) {
        setError("Nemate dozvolu da kreirate događaj.");
      } else {
        setError("Došlo je do greške prilikom kreiranja događaja.");
      }
    } finally {
      setLoading(false);
    }
  };

  const renderFieldError = (name) => {
    if (!fieldErrors[name]) return null;
    return (
      <div className="field-error">
        {fieldErrors[name].map((msg, i) => (
          <div key={i}>{msg}</div>
        ))}
      </div>
    );
  };

  return (
    <div className="create-event-page">
      <div className="create-event-card">
        <h1>Kreiranje događaja</h1>
        <p className="subtitle">
          Popuni formu kako bi kreirao novi događaj. Polja označena zvezdicom su obavezna.
        </p>

        {error && <div className="global-error">{error}</div>}

        <form onSubmit={handleSubmit} className="create-event-form">
          <div className="form-grid">
            <div className="field">
              <label>
                Naziv događaja <span className="req">*</span>
              </label>
              <input
                type="text"
                value={title}
                onChange={(e) => setTitle(e.target.value)}
                placeholder="npr. IT Konferencija 2026"
                required
              />
              {renderFieldError("title")}
            </div>

            <div className="field">
              <label>Opis</label>
              <textarea
                value={description}
                onChange={(e) => setDescription(e.target.value)}
                rows={4}
                placeholder="Kratak opis događaja..."
              />
              {renderFieldError("description")}
            </div>

            <div className="field">
              <label>
                Lokacija <span className="req">*</span>
              </label>
              <input
                type="text"
                value={location}
                onChange={(e) => setLocation(e.target.value)}
                placeholder="npr. Beograd, ETF"
                required
              />
              {renderFieldError("location")}
            </div>

            <div className="field">
              <label>
                Početak <span className="req">*</span>
              </label>
              <input
                type="datetime-local"
                value={startAt}
                onChange={(e) => setStartAt(e.target.value)}
                required
              />
              {renderFieldError("startAt")}
            </div>

            <div className="field">
              <label>
                Kraj <span className="req">*</span>
              </label>
              <input
                type="datetime-local"
                value={endAt}
                onChange={(e) => setEndAt(e.target.value)}
                required
              />
              {renderFieldError("endAt")}
            </div>

            <div className="field">
              <label>
                Kapacitet <span className="req">*</span>
              </label>
              <input
                type="number"
                min="1"
                value={capacity}
                onChange={(e) => setCapacity(e.target.value)}
                placeholder="npr. 150"
                required
              />
              {renderFieldError("capacity")}
            </div>

            <div className="field">
              <label>Kategorija (ID)</label>
              <select
                value={idCategory}
                onChange={(e) => setIdCategory(e.target.value)}
              >
                <option value="">Izaberi kategoriju</option>
                {categories.map((c) => (
                  <option key={c.idCategory ?? c.id} value={c.idCategory ?? c.id}>
                    {(c.idCategory ?? c.id) + " — " + (c.name ?? "")}
                  </option>
                ))}
              </select>
              {renderFieldError("idCategory")}
            </div>

            <div className="field">
              <label>Status</label>
              <select
                value={status}
                onChange={(e) => setStatus(e.target.value)}
              >
                <option value="ACTIVE">ACTIVE</option>
                <option value="CANCELLED">CANCELLED</option>
                <option value="DRAFT">DRAFT</option>
              </select>
              {renderFieldError("status")}
            </div>
          </div>

          <div className="actions">
            <button
              type="button"
              className="btn btn-ghost"
              onClick={() => navigate("/events")}
              disabled={loading}
            >
              Otkaži
            </button>
            <button type="submit" className="btn btn-primary" disabled={loading}>
              {loading ? "Čuvanje..." : "Sačuvaj događaj"}
            </button>
          </div>
        </form>
      </div>
    </div>
  );
}
