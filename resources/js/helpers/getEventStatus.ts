import { STATUS_OPTION_OBJ } from '@app/helpers/constants/eventFilterKeys'


export const getEventStatus = (type: string): string => {
  console.log(type, "type in get event status")
  switch (type) {
    case "checkPassed":
    case "customCheckPassed":
    case "dialResumed":
      return STATUS_OPTION_OBJ.CHECKED
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
      return STATUS_OPTION_OBJ.FAILED
  }
  return STATUS_OPTION_OBJ.ALL
}
