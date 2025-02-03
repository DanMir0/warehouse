import axios from "axios";

export const fetchMeasuringUnits = () => axios.get("/api/measuring_units");
export const deleteMeasuringUnit = (id) => axios.delete(`/measuring_units/${id}`);
export const fetchMeasuringUnit = (id) => axios.get(`/api/measuring_units/${id}`);
export const updateMeasuringUnit = (id, data) => axios.put(`/measuring_units/${id}`, data);
export const postMeasuringUnit = (data) => axios.post(`/measuring_units`, data);
