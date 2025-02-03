import axios from "axios";

export const fetchProducts = () => axios.get("/api/products");
export const deleteProduct = (id) => axios.delete(`/products/${id}`);
export const fetchMeasuringUnits = () => axios.get("/api/measuring_units");
export const fetchProduct = (id) => axios.get(`/api/product/${id}`);
export const postProduct = (data) => axios.post("/products", data);
export const updateProduct = (id, data) => axios.put(`/products/${id}`, data);
