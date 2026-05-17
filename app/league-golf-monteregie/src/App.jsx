import Standings from "./pages/Standings";

function App() {
  return (
    <div>
      <h1>Ligue de Golf Montérégie</h1>
      <div className="container">
        <Standings />
      </div>
      <div className="container">
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