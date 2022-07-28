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
