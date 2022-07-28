export const getLocaleCurrency = (currency: number, maximumFractionDigits = 0): string => {
  return currency.toLocaleString('ru-RU', {style: 'currency', currency: 'RUB', maximumFractionDigits})
}
