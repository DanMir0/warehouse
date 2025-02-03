import {ref} from "vue";

export default function useFormHandler() {
    const alertMessage = ref("");
    const alertType = ref("");
    const errors = ref({});

    const handlerResponse = async (promise, successMessage, errorMessage = "Не удалось получить данные!") => {
        try {
            const response = await promise;
            // return response.data;
            return {success: true, data: response.data}
        } catch (error) {
            if (error.response?.status === 422) {
                errors.value = { ...error.response.data.errors };
            }
            return { success: false, message: error.message };        }
    };

    return {
        alertMessage,
        alertType,
        errors,
        handlerResponse,
    }
}
