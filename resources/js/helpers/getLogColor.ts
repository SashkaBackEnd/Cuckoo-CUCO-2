//   endShift = 'Завершил работу',
//   startShift = 'Заступил на пост',
//   smsForHeadOfSecurity = 'Отправлено SMS-уведомление',
//   timeoutEndShift = 'Смена завершена принудительно',
//   shiftCanceled = 'Аннулирована смена',
//   guardActivate = 'Статус охранника изменен на активный',
//   guardDeactivate = 'Статус охранника изменен на неактивный',
//

import { LOG_COLORS, TLogColors } from '@app/helpers/logColors'


export const getLogColor = (type: string): TLogColors => {
  switch (type) {
    case "checkPassed":
    case "customCheckPassed":
    case "dialResumed":
      return LOG_COLORS.GREEN
    case "wrongPin":
    case "unknownPin":
    case "noGuardTimeExceeded":
    case 'shiftTimeExceeded':
    case 'objectGuardMismatch':
    case 'dialPaused':
    case 'autoSosEnd':
    case 'sosEnd':
    case 'sos':
    case 'customCheckFailed':
    case 'shortEndShiftTry':
    case 'checkFailed':
      return LOG_COLORS.RED
    default:
      return LOG_COLORS.BLUE
  }

}
