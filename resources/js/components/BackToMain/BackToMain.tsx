import React from 'react'
import {Link} from 'react-router-dom'
import {Icons} from '@components/UI/iconComponents'
import classes from './BackToMain.module.css'

export const BackToMain: React.FC = () => {
  return (
    <Link to="/entities" className={classes.BackToMenu}>
      <Icons.IconLeftArrow />К списку объектов
    </Link>
  )
}
