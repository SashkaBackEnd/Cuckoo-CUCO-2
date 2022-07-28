import React from 'react'
import {timeConverter} from '../../../timeConverter'
import classes from './Event.module.css'

const Event = (props) => {
  let log
  let img

  if (props.type === 'object') {
    switch (props.eventInfo.type) {
      case 'endShift':
        log = (
          <React.Fragment>
            <span>{props.eventInfo.securityGuard.shortName}&nbsp;</span> завершил работу
          </React.Fragment>
        )
        img = <img src="/images/svg/icon-warker.svg" alt="" />
        break
      case 'startShift':
        log = (
          <React.Fragment>
            <span>{props.eventInfo.securityGuard.shortName}&nbsp;</span> заступил на пост
          </React.Fragment>
        )
        img = <img src="/images/svg/icon-warker.svg" alt="" />
        break
      case '2MissedCall':
        log = <React.Fragment>Охрана не ответила на 2-ый звонок</React.Fragment>
        img = <img src="/images/svg/icon-call_fail.svg" alt="" />
        break
      case '1MissedCall':
        log = <React.Fragment>Охрана не ответила на 1-ый звонок</React.Fragment>
        img = <img src="/images/svg/icon-call_warning_first.svg" alt="" />
        break
      case 'checkPassed':
        log = (
          <React.Fragment>
            <span>Автоматическая&nbsp;</span>проверка поста прошла успешно
          </React.Fragment>
        )
        img = <img src="/images/svg/icon-success.svg" alt="" />
        break
      case 'checkFailed':
        log = (
          <React.Fragment>
            <span>Автоматическая&nbsp;</span>проверка поста не пройдена
          </React.Fragment>
        )
        img = <img src="/images/svg/icon-danger.svg" alt="" />
        break
      case 'smsForHeadOfSecurity':
        log = (
          <React.Fragment>
            Отправлено SMS-уведомление для&nbsp;<span>{props.eventInfo.guardedObject.shortNameHOS}</span>
          </React.Fragment>
        )
        img = <img src="/images/svg/icon-email.svg" alt="" />
        break
      case 'shiftCanceled':
        log = (
          <React.Fragment>
            Аннулирована смена для&nbsp;<span>{props.eventInfo.securityGuard.shortName}</span>
          </React.Fragment>
        )
        img = <img src="/images/svg/icon-cancel.svg" alt="" />
        break
      case 'dialPaused':
        log = <React.Fragment>Обзвон приостановлен</React.Fragment>
        img = <img src="/images/svg/icon-cancel.svg" alt="" />
        break
      case 'dialResumed':
        log = <React.Fragment>Обзвон возобновлен</React.Fragment>
        img = <img src="/images/svg/icon-cancel.svg" alt="" />
        break
      case 'customCheckPassed':
        log = <React.Fragment>Ручная проверка пройдена</React.Fragment>
        img = <img src="/images/svg/icon-success.svg" alt="" />
        break
      case 'customCheckFailed':
        log = <React.Fragment>Ручная проверка провалена</React.Fragment>
        img = <img src="/images/svg/icon-danger.svg" alt="" />
        break
      case 'shortEndShiftTry':
        log = (
          <React.Fragment>
            <span>{props.eventInfo.securityGuard.shortName}&nbsp;</span>Пытался завершить смену без сменщика
          </React.Fragment>
        )
        img = <img src="/images/svg/icon-cancel.svg" alt="" />
        break
      case 'timeoutEndShift':
        log = (
          <React.Fragment>
            Смена для&nbsp;
            <span>{props.eventInfo.securityGuard.shortName}&nbsp;</span> завершена принудительно
          </React.Fragment>
        )
        img = <img src="/images/svg/icon-warning.svg" alt="" />
        break
      case 'objectGuardMismatch':
        log = (
          <React.Fragment>
            <span>{props.eventInfo.securityGuard.shortName}&nbsp;</span> с одного поста пытается начать смену на другом
            посту
          </React.Fragment>
        )
        img = <img src="/images/svg/icon-warning.svg" alt="" />
        break
      case 'sos':
        log = <React.Fragment>Команда SOS</React.Fragment>
        img = <img src="/images/svg/icon-danger.svg" alt="" />
        break
      case 'sosEnd':
        log = <React.Fragment>Тревога снята вручную</React.Fragment>
        img = <img src="/images/svg/icon-success.svg" alt="" />
        break
      case 'autoSosEnd':
        log = <React.Fragment>Тревога снята автоматически</React.Fragment>
        img = <img src="/images/svg/icon-success.svg" alt="" />
        break
      case 'unknownPin':
        log = <React.Fragment>Введен неверный пинкод - {props.eventInfo.value1}</React.Fragment>
        img = <img src="/images/svg/icon-success.svg" alt="" />
        break
      case 'shiftTimeExceeded':
        log = (
          <React.Fragment>
            <span>{props.eventInfo.securityGuard.shortName}&nbsp;</span> Превышена максимальная продолжительность смены
          </React.Fragment>
        )
        img = <img src="/images/svg/icon-warning.svg" alt="" />
        break
      case 'noGuardTimeExceeded':
        log = <React.Fragment>Превышено время нахождения поста без охраны</React.Fragment>
        img = <img src="/images/svg/icon-success.svg" alt="" />
        break
      default:
        log = <React.Fragment>{props.eventInfo.type}</React.Fragment>
        img = <img src="/images/svg/icon-cancel.svg" alt="" />
        break
    }
  }

  if (props.type === 'guard') {
    switch (props.eventInfo.type) {
      case 'endShift':
        log = (
          <React.Fragment>
            <span>{props.name}&nbsp;</span> завершил работу
          </React.Fragment>
        )
        img = <img src="/images/svg/icon-warker.svg" alt="" />
        break
      case 'startShift':
        log = (
          <React.Fragment>
            <span>{props.name}&nbsp;</span> заступил на пост
          </React.Fragment>
        )
        img = <img src="/images/svg/icon-warker.svg" alt="" />
        break
      case '2MissedCall':
        log = (
          <React.Fragment>
            <span>{props.name}&nbsp;</span> пропущенный повторный звонок
          </React.Fragment>
        )
        img = <img src="/images/svg/icon-call_fail.svg" alt="" />
        break
      case '1MissedCall':
        log = (
          <React.Fragment>
            <span>{props.name}&nbsp;</span> пропущенный звонок
          </React.Fragment>
        )
        img = <img src="/images/svg/icon-call_warning_first.svg" alt="" />
        break
      case 'checkPassed':
        log = (
          <React.Fragment>
            <span>Автоматическая&nbsp;</span> проверка поста прошла успешно
          </React.Fragment>
        )
        img = <img src="/images/svg/icon-success.svg" alt="" />
        break
      case 'checkFailed':
        log = (
          <React.Fragment>
            <span>Автоматическая&nbsp;</span> проверка поста не пройдена
          </React.Fragment>
        )
        img = <img src="/images/svg/icon-danger.svg" alt="" />
        break
      case 'smsForHeadOfSecurity':
        log = (
          <React.Fragment>
            Отправлено SMS-уведомление для&nbsp;<span>{props.eventInfo.guardedObject.shortNameHOS}</span>
          </React.Fragment>
        )
        img = <img src="/images/svg/icon-email.svg" alt="" />
        break
      case 'shiftCanceled':
        log = (
          <React.Fragment>
            Аннулирована смена для&nbsp;<span>{props.name}</span>
          </React.Fragment>
        )
        img = <img src="/images/svg/icon-cancel.svg" alt="" />
        break
      case 'customCheckPassed':
        log = <React.Fragment>Ручная проверка пройдена</React.Fragment>
        img = <img src="/images/svg/icon-success.svg" alt="" />
        break
      case 'customCheckFailed':
        log = <React.Fragment>Ручная проверка провалена</React.Fragment>
        img = <img src="/images/svg/icon-danger.svg" alt="" />
        break
      case 'shortEndShiftTry':
        log = <React.Fragment>Пытался завершить смену без сменщика</React.Fragment>
        img = <img src="/images/svg/icon-cancel.svg" alt="" />
        break
      case 'timeoutEndShift':
        log = (
          <React.Fragment>
            Смена для <span>{props.name}&nbsp;</span> завершена принудительно
          </React.Fragment>
        )
        img = <img src="/images/svg/icon-warning.svg" alt="" />
        break
      case 'objectGuardMismatch':
        log = (
          <React.Fragment>
            <span>{props.name}&nbsp;</span> с одного поста пытается начать смену на другом посту
          </React.Fragment>
        )
        img = <img src="/images/svg/icon-warning.svg" alt="" />
        break
      case 'sos':
        log = <React.Fragment>Команда SOS</React.Fragment>
        img = <img src="/images/svg/icon-danger.svg" alt="" />
        break
      case 'sosEnd':
        log = <React.Fragment>Тревога снята вручную</React.Fragment>
        img = <img src="/images/svg/icon-success.svg" alt="" />
        break
      case 'autoSosEnd':
        log = <React.Fragment>Тревога снята автоматически</React.Fragment>
        img = <img src="/images/svg/icon-success.svg" alt="" />
        break
      case 'guardActivate':
        log = <React.Fragment>Статус охранника изменен на активный</React.Fragment>
        img = <img src="/images/svg/icon-success.svg" alt="" />
        break
      case 'guardDeactivate':
        log = <React.Fragment>Статус охранника изменен на неактивный</React.Fragment>
        img = <img src="/images/svg/icon-warning.svg" alt="" />
        break
      case 'shiftTimeExceeded':
        log = (
          <React.Fragment>
            <span>{props.name}&nbsp;</span> Превышена максимальная продолжительность смены
          </React.Fragment>
        )
        img = <img src="/images/svg/icon-warning.svg" alt="" />
        break
      default:
        log = (
          <React.Fragment>
            {props.eventInfo.type}
            <span> для {props.name}</span>
          </React.Fragment>
        )
        img = <img src="/images/svg/icon-cancel.svg" alt="" />
        break
    }
  }

  return (
    <div className={classes.Event}>
      <p>
        {img} {log}
      </p>
      <span>{timeConverter(props.eventInfo.date, 'time')}</span>
    </div>
  )
}

export default Event
