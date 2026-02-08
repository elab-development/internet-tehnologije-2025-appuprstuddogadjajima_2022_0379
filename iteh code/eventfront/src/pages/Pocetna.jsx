import React from "react";
import { Link } from "react-router-dom";
import "./Pocetna.css";
import FeatureCard from "../components/FeatureCard";
import { FaCalendarAlt, FaPlusCircle, FaUsers } from "react-icons/fa";

const Pocetna = () => {

  const features = [
  {
    id: 1,
    title: "Pregled događaja",
    description: "Prikaz svih dostupnih i nadolazećih događaja.",
    icon: FaCalendarAlt
  },
  {
    id: 2,
    title: "Kreiranje događaja",
    description: "Organizatori mogu lako dodavati nove događaje.",
    icon: FaPlusCircle
  },
  {
    id: 3,
    title: "Upravljanje korisnicima",
    description: "Administracija i kontrola učesnika.",
    icon: FaUsers
  }
];

  return (
    <div className="pocetna">

      
      <h1>Dobrodošli na našu stranicu za upravljanje događajima!</h1>

      
      <p>
        Ovde možete pregledavati nadolazeće događaje, kreirati nove događaje i
        upravljati postojećim događajima.
      </p>

      
      <div className="pocetna-actions">
        <Link to="/events" className="btn primary">Pregled događaja</Link>
        <Link to="/login" className="btn secondary">Prijava</Link>
      </div>

      
      <div className="features">
  {features.map(f => (
    <FeatureCard
      key={f.id}
      icon={f.icon}
      title={f.title}
      description={f.description}
    />
  ))}
</div>

      <p className="status">
        Sistem razvijen u okviru predmeta Internet tehnologije Ua blokaderi strokaderi.
      </p>

    </div>
  );
};

export default Pocetna;