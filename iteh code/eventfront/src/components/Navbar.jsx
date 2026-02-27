import React, { useEffect, useLayoutEffect } from 'react'
import { Link, useLocation } from 'react-router-dom';
import { useState } from 'react';
import "./Navbar.css";
import PrimaryButton from './PrimaryButton';
import api from '../api/api';
import { useNavigate } from 'react-router-dom';
const Navbar = () => {

  const location = useLocation();
  const navigate = useNavigate();
  console.log(location.pathname);
  const[isAuth, setIsAuth] = useState(false);
  useEffect(() => {
    const token = localStorage.getItem("token");
    setIsAuth(!!token);
  }, [location]);
  console.log("Location changed: ", location.pathname);
  console.log("isAuth:", isAuth);
  //console.log("Token",token);


  const handleLogout = async() => {
    try{
      await api.post("/logout");

    }catch(err){
      console.log("Logout error:", err);
    }finally{
      localStorage.removeItem("token");
      localStorage.removeItem("user");
      setIsAuth(false);
      navigate("/");
    }
  };

  return (
  <div className="navbar">
    <Link to="/" className="nav-link">Početna</Link>

    {!isAuth && (
      <>
        <Link to="/login" className="nav-link">Login</Link>
        <Link to="/register" className="nav-link">Registracija</Link>
      </>
    )}

    {isAuth && (
      <>
      <Link to="/events" className="nav-link">Moji eventovi</Link>
     <button className="nav-link logout-btn" onClick={handleLogout} type="button">
  Odjavi se
</button>
    
      </>
    )}
  </div>
);
}

export default Navbar

