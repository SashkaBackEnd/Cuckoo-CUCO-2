import React from 'react'
import classes from './Pictogram.module.css'

const Pictogram: React.FC = () => {
  return (
    <div className={classes.Pictogram}>
      <div className={classes.Circle} />
      <div className={classes.Stick} />
      <div className={classes.Circle} />
    </div>
  )
}

export default Pictogram
