import axios from "axios";

function apiBaseUrl() {
  if (process.env.REACT_APP_API_URL) {
    return process.env.REACT_APP_API_URL;
  }

  if (typeof window !== "undefined" && window.location.hostname.includes("railway.app")) {
    return "https://eventapi-production-5bc2.up.railway.app/api";
  }

  return "http://127.0.0.1:8000/api";
}

const api = axios.create({
  baseURL: apiBaseUrl(),
  withCredentials: false,
  headers: { "Content-Type": "application/json" },
});

api.interceptors.request.use((config) => {
  const token = sessionStorage.getItem("token") || localStorage.getItem("token");
  if (token) config.headers.Authorization = "Bearer " + token;
  return config;
});

export default api;