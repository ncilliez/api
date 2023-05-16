import { useState, useEffect } from 'react';
import axios from "axios";

function Show() {
    const [produits, setProduits] = useState([])
    const [page, setPage] = useState(1)
    const [stop, setStop] = useState(false)
    const items = 5;
    
    useEffect(() => {
        axios
          .get(`http://192.168.1.10/api/produits?items=${items}&page=${page}`)
          .then(response => {setProduits(response.data);})
          .catch(error => console.error(error));

        const pageNext = page+1;
        axios
          .get(`http://192.168.1.10/api/produits?items=${items}&page=${pageNext}`)
          .then(response => {
            if (response.data.length === 0) {
                setStop(true);
            }else{
                setStop(false); 
            }
          })
          .catch(error => console.error(error));
      }, [page]);

    const handleNext = () => {
        if(stop === false){
            setPage(page+1);
        }
    }

    const handlePrevent = () =>{
        if(page !== 1){
            setPage(page-1);
        }
    }

    return (
        <div>
            <p>Page: {page}</p>
            <ul>
            {produits.map((une, i) => (
                        <li key={i}>
                            {une.name}
                            <img src={une.image_produit} alt={une.name} style={{width: "100px"}} />
                        </li>
                    ))}                    
            </ul>    
            <button onClick={handlePrevent} disabled={page === 1}>Précédent</button>
            <button onClick={handleNext} disabled={stop === true}>Suivant</button>
        </div>
    );
}

export default Show;