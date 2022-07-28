import React from 'react'
import {Link} from 'react-router-dom'
import {Icons} from '@components/UI/iconComponents'
import classes from '../BackToMain/BackToMain.module.css'


export const BackToEmployees: React.FC = () => {
  return (
    
    <Link to="/managers" className={classes.BackToMenu}>
      <Icons.IconLeftArrow />Вернуться к списку сотрудников
    </Link>
  )
}
