import React, { useEffect, useState } from "react";
import { MapContainer, Marker, Popup, TileLayer } from "react-leaflet";
import L from "leaflet";
import api from "../api/api";
import "leaflet/dist/leaflet.css";

delete L.Icon.Default.prototype._getIconUrl;
L.Icon.Default.mergeOptions({
  iconRetinaUrl: require("leaflet/dist/images/marker-icon-2x.png"),
  iconUrl: require("leaflet/dist/images/marker-icon.png"),
  shadowUrl: require("leaflet/dist/images/marker-shadow.png"),
});

export default function EventLocationMap({ location, title }) {
  const [point, setPoint] = useState(null);
  const [status, setStatus] = useState("loading");

  useEffect(() => {
    const query = String(location ?? "").trim();
    if (query.length < 2) {
      setPoint(null);
      setStatus("empty");
      return;
    }

    let cancelled = false;
    setStatus("loading");
    setPoint(null);

    api
      .get("/geocode", { params: { q: query } })
      .then((res) => {
        if (cancelled) {
          return;
        }
        setPoint({
          lat: res.data.lat,
          lon: res.data.lon,
          label: res.data.label ?? query,
        });
        setStatus("ready");
      })
      .catch(() => {
        if (!cancelled) {
          setPoint(null);
          setStatus("missing");
        }
      });

    return () => {
      cancelled = true;
    };
  }, [location]);

  if (status === "empty") {
    return null;
  }

  if (status === "loading") {
    return <div className="event-map-status">Učitavanje mape...</div>;
  }

  if (status === "missing" || !point) {
    return (
      <div className="event-map-wrap">
        <div className="event-map-status event-map-status-overlay">
          Tačna adresa nije pronađena. Prikazana je mapa Srbije.
        </div>
        <MapContainer
          center={[44.8, 20.45]}
          zoom={7}
          scrollWheelZoom={false}
          className="event-map"
        >
          <TileLayer
            attribution='&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
            url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
          />
        </MapContainer>
      </div>
    );
  }

  const position = [point.lat, point.lon];

  return (
    <div className="event-map-wrap">
      <MapContainer
        key={`${point.lat}-${point.lon}`}
        center={position}
        zoom={13}
        scrollWheelZoom={false}
        className="event-map"
      >
        <TileLayer
          attribution='&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
          url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
        />
        <Marker position={position}>
          <Popup>
            <strong>{title}</strong>
            <br />
            {point.label}
          </Popup>
        </Marker>
      </MapContainer>
    </div>
  );
}
