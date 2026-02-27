import React, { useState } from "react";
import "./LoginPage.css";
import api from "../api/api";
import { useNavigate } from "react-router-dom";
import PrimaryButton from "../components/PrimaryButton";
export const LoginPage = () => {

  const navigate = useNavigate();

  const [email, setEmail] = useState("testzaverifikaciju1231@gmail.com");
  const [password, setPassword] = useState("test12345678");

  const[loading, setLoading] = useState(false);
  const[error, setError] = useState("");
  const[info, setInfo] = useState("");


  const handleSubmit = async (e) => {
    e.preventDefault();
    setLoading(true);
    setError("");
    setInfo("");

    try{
  const res = await api.post('/login', { email, password });
    const{token, user, message}= res.data;

      localStorage.setItem("token", token);
      localStorage.setItem("user", JSON.stringify(user));
console.log("LS TOKEN:", localStorage.getItem("token"));

    setInfo(message|| "Uspesna prijava");
    setLoading(false);





      navigate("/events");




     // console.log("Uspesna prijava", token, user, message);
    //console.log(res.data);
    //console.log(res);




    
    }catch(err){
      setLoading(false);

      console.log(err);

      if(err.response.status === 401){
        setError("Neispravna email adresa ili lozinka");
      }else if(err.response.status === 422){
        setError("Nedostaju potrebni podaci za prijavu");
      }else{
        setError("Došlo je do greške prilikom prijave. Pokušajte ponovo.");
      }



    }



  


  

    // za sada samo logujemo (kasnije ide API)
    //console.log("Pritisnuto dugme za prijavu");
    //console.log("Email:", email);
    //console.log("Password:", password);
  };

  return (
    <div className="login-container">
      <form className="login-card" onSubmit={handleSubmit}>
        <h2>Prijava na EventHub</h2>

        <div className="form-group">
          <label>Email</label>
          <input
            type="email"
            placeholder="Unesite email"
            value={email}
            onChange={(e) => setEmail(e.target.value)}
            required
          />
        </div>

        <div className="form-group">
          <label>Lozinka</label>
          <input
            type="password"
            placeholder="Unesite lozinku"
            value={password}
            onChange={(e) => setPassword(e.target.value)}
            required
          />
        </div>
        {info && <div className="auth-alert auth-alert-info">{info}</div>}
        {error && <div className="auth-alert auth-alert-error">{error}</div>}
            <PrimaryButton type="submit" loading={loading} loadingText="Prijavljivanje...">
              Prijavi se
            </PrimaryButton>
      </form>
    </div>
  );
};