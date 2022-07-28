export const unmaskPhone = (phone: string): string => {
  return phone?.replace(/[^+\d]/g, '')
}

