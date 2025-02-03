import axios from "axios";

export const fetchCounterparties = () =>   axios.get("/api/counterparties");
export const deleteCounterparties = (id) =>   axios.delete(`/counterparties/${id}`);
export const fetchCounterparty = (id) => axios.get(`/api/counterparties/${id}`);
export const postCounterparty = (data) => axios.post("/counterparties", data);
export const updateCounterparty = (id, data) => axios.put(`/counterparties/${id}`, data);
