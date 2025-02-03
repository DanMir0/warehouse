import axios from "axios";

export const fetchOrderStatuses = () => axios.get("/api/order_statuses");
export const deleteOrderStatuses = (id) => axios.delete(`/order_statuses/${id}`);
export const fetchOrderStatus = (id) => axios.get(`/api/order_statuses/${id}`);

export const postOrderStatus = (data) => axios.post("/order_statuses", data);
export const updateOrderStatus = (id, data) => axios.put(`/order_statuses/${id}`, data);
