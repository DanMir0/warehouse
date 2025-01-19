export const requireRule = value => {
    if (value) return true;
    return "Поле обязательное"
}

export const innRule = value => /^[0-9]{10}$/.test(value) || "ИНН должен содержать 10 цифр.";

export const positiveNumberRule = value => {
    if (value && value > 0) return true;
    return "Введите положительное число"
}
