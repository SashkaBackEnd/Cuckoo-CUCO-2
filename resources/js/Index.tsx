import React, { useEffect } from 'react'

import axios from 'axios'
import { Route, Switch, Redirect, withRouter } from 'react-router-dom'

import { IRoute, privateRoutes, RouteWithSubRoutes } from './Routes'
import { AuthPage } from '@pages/AuthPage'
import { Layout } from '@components/Layout'
import { authApi, TOKEN_KEY } from './api'
import { PermissionTypes } from '@components/AccessSettings/ManagerInAccess'
import { useCurrentUser } from '@hooks/useCurrentManager'
import { Loader } from '@components/UI/Loader'
import { toast } from '@app/theme'


const Index: React.FC = () => {
  const token = localStorage.getItem(TOKEN_KEY)
  const isAuth = !!token

  let routes = (
    <Switch>
      <Route path="/" component={AuthPage}/>
      <Redirect to={'/'}/>
    </Switch>
  )

  const { manager, isLoading, isError }= useCurrentUser()

  if (isAuth) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
    let changedRoutes: IRoute[]
    if (!!manager) {

      const access = JSON.parse(manager.access)

      if (manager.roleType === 2) {
        changedRoutes = privateRoutes?.filter(route => {
          if (access[route.routeName] === PermissionTypes.view ||
            access[route.routeName] === PermissionTypes.edit) {
            route.isShowMenu = true
          }

          return access[route.routeName] === PermissionTypes.view ||
            access[route.routeName] === PermissionTypes.edit || !route.hasOwnProperty("isShowMenu")
        })
      } else if (manager.roleType === 1) {
        changedRoutes = privateRoutes?.filter(route => {
          if (route.hasOwnProperty('isShowMenu')) {
            route.isShowMenu = true
          }
          return true
        })
      }
    }

    if (isLoading) {
      return <Loader/>
    }

    if (isError){
      authApi.logout()
      window.location.reload()
      toast({
        status: "error",
        title: "Кто то вошел из другого девайса"
      })
    }


    const mainPaths = changedRoutes.filter(({ isShowMenu }) => isShowMenu)
    const mainPath = mainPaths.length ? mainPaths[0].path : '/profile'

    routes = (
      <Switch>
        {changedRoutes?.map((route, i) => (
          <RouteWithSubRoutes key={route.path + i} {...route} />
        ))}
        {<Redirect to={mainPath}/>}
      </Switch>
    )
  }

  return (
    <div className="App">
      <Layout isAuth={isAuth}>{routes}</Layout>
    </div>
  )
}

export default withRouter(Index)
