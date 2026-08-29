import React, { useEffect, useLayoutEffect } from 'react'
import { Link, NavLink, useLocation } from 'react-router-dom';
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
  const[unread, setUnread] = useState(0);
  useEffect(() => {
    const token = localStorage.getItem("token");
    setIsAuth(!!token);
    if (!token) {
      setUnread(0);
      return;
    }

    const loadUnread = async () => {
      try {
        const res = await api.get("/notifications");
        const list = Array.isArray(res.data) ? res.data : [];
        setUnread(list.filter((n) => !n.seen).length);
      } catch {
        setUnread(0);
      }
    };

    loadUnread();
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
    <NavLink to="/" end className="nav-link">Početna</NavLink>
    <NavLink to="/events" className="nav-link">Događaji</NavLink>
    <NavLink to="/calendar" className="nav-link">Kalendar</NavLink>

    {!isAuth && (
      <>
        <Link to="/login" className="nav-link">Login</Link>
        <Link to="/register" className="nav-link">Registracija</Link>
      </>
    )}

    {isAuth && (
      <>
        <NavLink to="/my-events" className="nav-link">Moje prijave</NavLink>
        <NavLink to="/notifications" className="nav-link">
          Obaveštenja
          {unread > 0 && <span className="nav-badge">{unread > 9 ? "9+" : unread}</span>}
        </NavLink>
        <button className="nav-link logout-btn" onClick={handleLogout} type="button">
          Odjavi se
        </button>
      </>
    )}
  </div>
);
}

export default Navbar

