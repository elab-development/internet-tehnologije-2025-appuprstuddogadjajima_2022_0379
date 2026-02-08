import logo from './logo.svg';
import './App.css';
import { BrowserRouter, Routes, Route } from 'react-router-dom';
//import Home from './components/Home';
//import Events from './components/Events';
//import EventDetails from './components/EventDetails';
//import CreateEvent from './components/CreateEvent';
import Pocetna from './pages/Pocetna';
import { LoginPage } from './pages/LoginPage';
import Navbar from './components/Navbar';
import Events from './pages/Events';
import EventDetails from './pages/EventDetails';
import CreateEvent from './pages/CreateEvent';
import Footer from './components/Footer';
import { RegisterPage } from "./pages/Register";

function App() {
  return (
    <div>
      <BrowserRouter>
      <Navbar>  </Navbar>
        <Routes>
           <Route path="/events" element={<Events />} />
          <Route path="/" element={<Pocetna />} />
          <Route
  path="/login"
  element={
    <div className="page">
      <LoginPage />
    </div>
  }
/>

<Route
  path="/register"
  element={
    <div className="page">
      <RegisterPage />
    </div>
  }
/>



          {/*<Route path="/" element={<Home />} />
          <Route path="/events" element={<Events />} />
          <Route path="/events/:id" element={<EventDetails />} />
          <Route path="/create-event" element={<CreateEvent />} />*/}

        </Routes>

        <Footer/> 
      </BrowserRouter>



    </div>
  );
}

export default App;
