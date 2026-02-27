import React, { useState } from "react";
import "./Register.css";
import api from "../api/api";
import { useNavigate } from "react-router-dom";
import TextInput from "../components/TextInput";
import PrimaryButton from "../components/PrimaryButton";

export const RegisterPage = () => {
  const navigate = useNavigate();

  const [firstName, setFirstName] = useState("Luka");
    const [lastName, setLastName] = useState("Konatar");
  const [email, setEmail] = useState("konatarluka1@gmail.com");
  const [password, setPassword] = useState("12345678");
  const [passwordConfirm, setPasswordConfirm] = useState("12345678");

  const [loading, setLoading] = useState(false);
  const [error, setError] = useState("");
  const [info, setInfo] = useState("");

  const handleSubmit = async (e) => {
  e.preventDefault();
  setLoading(true);
  setError("");
  setInfo("");

  if (password !== passwordConfirm) {
    setError("Lozinke se ne poklapaju");
    setLoading(false);
    return;
  }

  try {
    const formData = new FormData();
    formData.append("firstName", firstName);
    formData.append("lastName", lastName);
    formData.append("email", email);
    formData.append("password", password);
    formData.append("password_confirmation", passwordConfirm);

    const res = await api.post("/register", formData);

    setInfo(res.data?.message || "Registracija uspešna. Prijavite se.");
    setTimeout(() => navigate("/login"), 1500);
  } catch (err) {
    const status = err?.response?.status;

    if (status === 422) {
      // console.log(err.response.data.errors);
      console.log(err.response.data);

      setError("Neispravni ili nepotpuni podaci");
    } else {
      setError("Greška prilikom registracije. Pokušajte ponovo.");
    }
  } finally {
    setLoading(false);
  }
};

  return (
    <div className="login-container">
      <form className="login-card" onSubmit={handleSubmit}>
        <h2>Registracija</h2>
<div className="name-row">
  <TextInput
    placeholder="Ime"
    value={firstName}
    onChange={(e) => setFirstName(e.target.value)}
    required
  />

  <TextInput
    placeholder="Prezime"
    value={lastName}
    onChange={(e) => setLastName(e.target.value)}
    required
  />
</div>

<TextInput
  type="email"
  placeholder="Email"
  value={email}
  onChange={(e) => setEmail(e.target.value)}
  required
/>
        <TextInput
  type="password"
  placeholder="Lozinka"
  value={password}
  onChange={(e) => setPassword(e.target.value)}
  required
/>

     <TextInput
  type="password"
  placeholder="Potvrda lozinke"
  value={passwordConfirm}
  onChange={(e) => setPasswordConfirm(e.target.value)}
  showPasswordToggle
  required
/>
        {info && <div className="auth-alert auth-alert-info">{info}</div>}
        {error && <div className="auth-alert auth-alert-error">{error}</div>}

       <PrimaryButton type="submit" loading={loading} loadingText="Registracija...">
  Registruj se
</PrimaryButton>


      </form>
    </div>
  );
};
