import axios from "axios";

export const fetchTechCards = () => axios.get("/api/tech_cards");
export const deleteTechCard = (id) => axios.delete(`/tech_cards/${id}`);
export const fetchProducts = () =>  axios.get("/api/products");
export const fetchTechCard = (id) => axios.get(`/api/tech_cards/${id}`);
export const fetchTechCardProduct = (id) => axios.get(`/api/tech_card_products/${id}`);
export const postTechCard = (data) => axios.post("/tech_cards", data);
export const updateTechCard = (id, data) => axios.put(`/tech_cards/${id}`, data);
