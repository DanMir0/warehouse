export function clearAlertAfterDelay(setter, delay = 5000) {
    setTimeout(() => {
        setter("");
    }, delay);
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
