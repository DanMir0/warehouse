import axios from "axios";

export const fetchOrders = () => axios.get("/api/orders");
export const deleteOrder = (id) => axios.delete(`/orders/${id}`);
export const fetchCounterparties = () => axios.get("/api/counterparties");
export const fetchOrderStatuses = () => axios.get("/api/order_statuses");
export const fetchOrder = (id) => axios.get(`/api/orders/${id}`);
export const fetchOrderTechCard = (id) => axios.get(`/api/order_tech_card/${id}`);
export const postOrder = (data) => axios.post("/orders", data);
export const updateOrder = (id, data) => axios.put(`/orders/${id}`, data);
export const updateOrderStatus = (id, data) => axios.put(`/api/orders/${id}`, data);
export const fetchDocument = (id) => axios.get(`/api/orders/documents/${id}`);
