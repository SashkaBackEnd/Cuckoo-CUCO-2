
export const getShiftWordInRussian = (shiftCount: number = 0): string => {
  switch (shiftCount) {
    case 0:
      return 'сменов'
    case 1:
      return 'смена'
    case 2 || 3 || 4:
      return 'смены'
    default :
      return 'сменов'
  }
}
