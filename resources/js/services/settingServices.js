import axios from "axios";

export const postSetting = (data) => axios.post("/settings",data);
export const getSettings = () => axios.get("/api/settings");
