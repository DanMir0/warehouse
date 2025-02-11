import axios from "axios";

export const fetchDocuments = () => axios.get("/api/documents");
export const fetchDocument = (id) => axios.get(`/api/document/${id}`);
export const fetchDocumentTypes = () => axios.get("/api/document_types");
export const fetchCounterparties = () => axios.get("/api/counterparties");
