import axios from "axios";

export const fetchDocuments = () => axios.get("/api/documents");
export const fetchDocument = (id) => axios.get(`/api/documents/${id}`);
export const fetchDocumentTypes = () => axios.get("/api/document_types");
export const fetchCounterparties = () => axios.get("/api/counterparties");
export const fetchDocumentProducts = (id) => axios.get(`/api/documents_products/${id}`);
export const updateDocument = (id, data) => axios.put(`/documents/${id}`, data);
export const addDocument = (data) => axios.post("/documents", data);
