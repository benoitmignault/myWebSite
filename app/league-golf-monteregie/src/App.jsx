import Standings from "./pages/Standings";
import EventsList from "./pages/EventsList";

function App() {
  return (
    <div>
      <h1>Ligue de Golf Montérégie</h1>
      <div className="main-container">        
        <div className="sub-container">
          <Standings />
        </div>
        <div className="sub-container">
          <EventsList />
        </div>
      </div>
     
      <button
        className="scroll-top"
        onClick={() =>
            window.scrollTo({
                top: 0,
                behavior: "smooth"
            })
        }
      > 
        ↑
      </button>      
    </div>
  );
}

export default App;