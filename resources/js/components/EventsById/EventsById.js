import React from 'react'
import classes from './EventsById.module.css'
import Event from './Event/Event'

const EventsById = (props) => {
  let eventList
  if (props.events) {
    eventList = props.events.map((eventInfo, index) => {
      return <Event name={props.name} type={props.type} key={eventInfo.id} eventInfo={eventInfo} />
    })
  }

  return (
    <div className={classes.EventsById}>
      <p>События за последние 24 часа</p>
      <div>
        {props.events ? (
          eventList.reverse()
        ) : (
          <React.Fragment>
            <p className={classes.EventsByIdEmpty}>
              Здесь будут выводится события, происходящие на посту или с охранником. Пока еще ничего не произошло
            </p>
            <img src="/images/img-security.jpg" alt="empty-log" />
          </React.Fragment>
        )}
      </div>
    </div>
  )
}

export default EventsById
