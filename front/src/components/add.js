import React, { useState } from 'react';
import axios from 'axios';

function AddProductForm() {
    const [name, setName] = useState('');
    const [description, setDescription] = useState('');
    const [price, setPrice] = useState('');
    const [category, setCategory] = useState('');
    const [image, setImage] = useState(null);
    const [previewImage, setPreviewImage] = useState(null);

    const handleSubmit = async (event) => {
        event.preventDefault();
        // Créez un objet FormData pour envoyer les données du formulaire
        const formData = new FormData();
        formData.append('name', name);
        formData.append('description', description);
        formData.append('price', price);
        formData.append('category', category);
        formData.append('image_produit', image);

        try {
            // Envoyez les données à votre API en utilisant Axios
            const response = await axios.post('http://192.168.1.10/api/produits', formData);

            // Vérifiez la réponse de l'API
            if (response.status === 200) {
                const data = response.data;
                // Traitez la réponse de l'API en conséquence
                console.log(data);
            } else {
                // Gérez les erreurs de l'API
                console.error('Une erreur s\'est produite lors de la requête.');
            }
        } catch (error) {
            // Gérez les erreurs liées à la requête
            console.error('Une erreur s\'est produite lors de la requête.', error);
        }
    };
  
    const handleImageChange = (event) => {
        const selectedImage = event.target.files[0];// récupére l'image selectionné dans l'input type file
        setImage(selectedImage);
        setPreviewImage(URL.createObjectURL(selectedImage));// ajoute l'image au state previewImage
    };

    return (
        <div>
            <form onSubmit={handleSubmit}>
                <label>Nom du produit:
                    <input type="text" value={name} onChange={(event) => setName(event.target.value)}/>
                </label>
                <label>Description:
                    <textarea value={description} onChange={(event) => setDescription(event.target.value)}></textarea>
                </label>
                <label>Prix:
                    <input type="number" value={price} onChange={(event) => setPrice(event.target.value)}/>
                </label>
                <label>Catégorie:
                    <input type="text" value={category} onChange={(event) => setCategory(event.target.value)}/>
                </label>
                <label>Image du produit:
                    <input type="file" onChange={handleImageChange}/>
                </label>
                    {previewImage && (
                        <div>
                        <img src={previewImage} alt="" style={{width: "100px"}}/>
                        </div>
                    )}
                <button type="submit">Ajouter</button>
            </form>
        </div>
    );
}

export default AddProductForm;
