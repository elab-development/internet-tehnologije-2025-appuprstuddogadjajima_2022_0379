import axios from "axios";

const LOCAL_API = "http://127.0.0.1:8000/api";
const RAILWAY_API = "https://eventapi-production-5bc2.up.railway.app/api";

const api = axios.create({
  baseURL: LOCAL_API,
  withCredentials: false,
  headers: { "Content-Type": "application/json" },
});

api.interceptors.request.use((config) => {
  if (typeof window !== "undefined" && window.location.hostname.includes("railway.app")) {
    config.baseURL = RAILWAY_API;
  } else if (process.env.REACT_APP_API_URL) {
    config.baseURL = process.env.REACT_APP_API_URL;
  }

  const token = sessionStorage.getItem("token") || localStorage.getItem("token");
  if (token) {
    config.headers.Authorization = "Bearer " + token;
  }

  return config;
});

export default api;
