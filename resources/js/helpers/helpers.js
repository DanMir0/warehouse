/**
 * @param {Ref} alertMessageRef - Ref для сообщения алерта.
 * @param {Ref} alertTypeRef - Ref для типа алерта.
 * @param {string} message - Сообщение алерта.
 * @param {string} type - Тип алерта (success, error и т.д.).
 * @param {number} delay - Задержка перед очитской сообщений (в миллисекундах)
 */

export function setAlert(alertMessageRef, alertTypeRef, message, type, delay= 4000) {
    alertMessageRef.value = message;
    alertTypeRef.value = type;

    if (delay > 0) {
        setTimeout(() => {
            alertMessageRef.value = "";
            alertTypeRef.value = "";
        }, delay)
    }
}

export function formatDate(date) {
    if (!date) return "";
    return new Date(date).toISOString().split("T")[0];
}

export function compareObjData(obj1, obj2) {
    if (obj1 === obj2) return true; // Простое равенство

    if (typeof obj1 !== "object" || typeof obj2 !== "object" || obj1 === null || obj2 === null) {
        return false; // Если это не объекты, то возвращаем false
    }

    const keys1 = Object.keys(obj1);
    const keys2 = Object.keys(obj2);

    if (keys1.length !== keys2.length) return false; // Разное количество ключей

    for (const key of keys1) {
        if (!keys2.includes(key)) return false; // Ключ отсутствует в obj2

        const val1 = obj1[key];
        const val2 = obj2[key];

        const areObjects = typeof val1 === "object" && typeof val2 === "object";
        if (areObjects && !compareObjData(val1, val2)) return false; // Рекурсивная проверка вложенных объектов
        if (!areObjects && val1 !== val2) return false; // Простое сравнение значений
    }

    return true; // Всё совпадает;
}

export function formatPhone(value) {
    value = value.replace(/\D/g, "")
    if (value.startsWith("7")) {
        value = "+" + value
    } else {
        value = "+7" + value;
    }

    if (value.length > 2) value = value.slice(0, 2) + " (" + value.slice(2);
    if (value.length > 7) value = value.slice(0, 7) + ") " + value.slice(7);
    if (value.length > 12) value = value.slice(0, 12) + "-" + value.slice(12);
    if (value.length > 15) value = value.slice(0, 15) + "-" + value.slice(15);
    if (value.length > 18) value = value.slice(0, 18);

    return  value;
}
