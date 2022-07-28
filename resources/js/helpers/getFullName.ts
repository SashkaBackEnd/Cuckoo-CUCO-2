export const getFullName = (
  surname: string, name?: string, patronymic?: string): string => {
  return `${surname} ${name ? name.charAt(0) + '.' : ''} ${patronymic
    ? patronymic.charAt(0) + '.'
    : ''}`
}

export const getNameSurname = (fullName: string): { firstName: string, lastName: string } => {
  const nameArr = fullName.split(' ')

  return { firstName: nameArr[0], lastName: nameArr[1] }
}
