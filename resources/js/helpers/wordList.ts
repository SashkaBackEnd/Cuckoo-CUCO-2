export const managersWordInRussian = (managerCount: number): string => {
  switch (managerCount) {
    case 1:
      return 'менеджер'
    case 2 || 3 || 4 :
      return 'менеджера'
    default :
      return 'менеджеров'
  }
}

export const workersWordInRussian = (workersCount: number): string => {
  switch (workersCount) {
    case 1:
      return 'работник'
    case 2:
    case 3 :
    case 4:
      return 'работника'
    default :
      return 'работников'
  }
}

export const guardedObjectsWordInRussian = (postCount: number = 0): string => {
  if (postCount >= 5 && postCount <= 20) return 'постов'
  if (postCount >= 2 && postCount <= 4) return 'поста'

  switch (postCount) {
    case 0:
      return 'постов'
    case 1:
      return 'пост'
    default :
      return 'пост'
  }
}

export const getPostNameInRussian = (postCount: number = 0): string => {

  if (postCount >= 5 && postCount <= 20) return 'постов'
  switch (postCount) {
    case 0:
      return 'постов'
    case 1:
      return 'пост'
    case 2 || 3 || 4:
      return 'поста'
    default :
      return 'пост'
  }
}

export const entityCountWordInRussian = (workersCount: number): string => {
  switch (workersCount) {
    case 1:
      return 'объект'
    case 2 || 3 || 4:
      return 'объекта'
    default :
      return 'объектов'
  }
}

export const errorMessageFrom = (errorData) => {
  const tempArr = errorData.split(',')[0].split(':')[1].split('')
  return tempArr.slice(1, tempArr.length - 1).join('')
}
