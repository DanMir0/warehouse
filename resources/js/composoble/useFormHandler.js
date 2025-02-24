import {ref} from "vue";

export default function useFormHandler() {
    const alertMessage = ref("");
    const alertType = ref("");
    const errors = ref({});

    const handlerResponse = async (promise, successMessage, errorMessage = "Не удалось получить данные!") => {
        try {
            const response = await promise;

            return {success: true, data: response.data}
        } catch (error) {
            if (error.response) {
                const status = error.response.status;
                if (status === 422) {
                    errors.value = { ...error.response.data.errors };
                } else if (status === 500 || status === 400) {
                    // Проверяем, есть ли в ответе детализированная ошибка о нехватке материалов
                    const errorMessage = error.response.data.message || "Произошла ошибка!";
                    const errorDetails = error.response.data.error; // Может содержать JSON с товарами

                    if (errorDetails.includes("Недостаточно материалов")) {
                        return {
                            success: false,
                            message: errorMessage,
                            details: errorDetails
                        };
                    }
                    return { success: false, message: errorMessage };
                }
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
