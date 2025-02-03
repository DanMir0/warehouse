import axios from "axios";

export const fetchDocumentTypes = () => axios.get("/api/document_types");
export const deleteDocumentTypes = (id) => axios.delete(`/document_types/${id}`);
export const fetchDocumentType = (id) => axios.get(`/api/document_types/${id}`);
export const postDocumentType = (data) => axios.post("/document_types", data);
export const putDocumentType = (id, data) => axios.put(`/document_types/${id}`, data);
