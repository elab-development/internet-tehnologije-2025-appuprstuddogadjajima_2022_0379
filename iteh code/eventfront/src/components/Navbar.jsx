import React, { useEffect, useLayoutEffect } from 'react'
import { Link, useLocation } from 'react-router-dom';
import { useState } from 'react';
import "./Navbar.css";
const Navbar = () => {

  const location = useLocation();
  console.log(location.pathname);
  const[isAuth, setIsAuth] = useState(false);
  useEffect(() => {
    const token = localStorage.getItem("token");
    setIsAuth(!!token);
  }, [location]);
  console.log("Location changed: ", location.pathname);
  console.log("isAuth:", isAuth);
  //console.log("Token",token);

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
      <Link to="/events" className="nav-link">Moji eventovi</Link>
    )}
  </div>
);
}

export default Navbar

