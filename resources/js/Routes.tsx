import React, { ComponentType } from 'react'
import { Route } from 'react-router-dom'
import { ErrorBoundary } from 'react-error-boundary'
import { EntityEditPage } from '@pages/EntityEditPage'
import { EntityCreatePage } from '@pages/EntityCreatePage'
import { PostCreatePage } from '@pages/PostCreatePage'
import { EntityPage } from '@pages/EntityPage'
import { PostPage } from '@pages/PostPage'
import { EntitiesPage } from '@pages/EntitiesPage'
import { ReportsPage } from '@pages/ReportsPage'
import { PageTransition } from '@components/UI/PageTransition'
import { ProfileEditPage } from '@pages/ProfileEditPage'
import { WorkersPage } from '@pages/WorkersPage'
import { WorkerPage } from '@pages/WorkerPage'
import { WorkerCreatePage } from '@pages/WorkerCreatePage'
import { WorkerEditPage } from '@pages/WorkerEditPage/WorkerEditPage'
import { Icons } from '@components/UI/iconComponents'
import { PostEditPage } from '@pages/PostEditPage'
import { ErrorFallback } from '@components/UI/ErrorFallback'
import { ManagersPage } from '@pages/ManagersPage'
import { ManagerCreatePage } from '@pages/ManagerCreatePage'
import { ManagerPage } from '@pages/ManagerPage'
import {
  AccessSettingsPage,
} from '@pages/AccessSettingsPage'
import { ManagerEditPage } from '@pages/ManagerEditPage/ManagerEditPage'
import { EventData } from '@pages/EventData'




import { LogDataPage } from '@pages/LogDataPage/LogDataPage'


export interface IRoute {
  path: string
  label?: string
  isShowMenu?: boolean
  exact?: boolean
  icon?: typeof Icons.IconMenuEntities
  component?: ComponentType
  routes?: IRoute[]
  fallback?: Element | null
  routeName?: string
  mobileQueueNum?: number
}

export const ROUTE_NAMES = {
  objects: "objects",
  managers: "managers",
  workers: "workers",
  reports: "reports",
  logs: "logs",
}



export const privateRoutes: IRoute[]  = [
  {
    path: '/profile',
    component: ProfileEditPage,
    mobileQueueNum: 0,
  },
  {
    path: '/entities/edit/:entityId',
    component: EntityEditPage,
    mobileQueueNum: 3,
  },
  {
    path: '/entities/create',
    component: EntityCreatePage,
    mobileQueueNum: 3,
  },
  {
    path: '/entities/:entityId/create',
    component: PostCreatePage,
    mobileQueueNum: 3,

  },
  {
    path: '/entities/:entityId/edit/:postId',
    component: PostEditPage,
    mobileQueueNum: 3,

  },
  {
    path: '/entities/:entityId',
    component: EntityPage,
    mobileQueueNum: 3,
    routes: [
      {
        path: '/entities/:entityId/:postId',
        component: PostPage,
        mobileQueueNum: 3,
      },
    ],
  },
  {
    path: '/entities',
    label: 'Объекты',
    isShowMenu: false,
    exact: true,
    component: EntitiesPage,
    icon: Icons.IconMenuEntities,
    routeName: ROUTE_NAMES.objects,
    mobileQueueNum: 3,
  },
  {
    path: '/workers/edit/:workerId',
    component: WorkerEditPage,
    mobileQueueNum: 1,
  },
  {
    path: '/workers/create',
    component: WorkerCreatePage,
    mobileQueueNum: 1,
  },
  {
    path: '/workers',
    label: 'Работники',
    isShowMenu: false,
    component: WorkersPage,
    icon: Icons.IconMenuWorkers,
    routeName: ROUTE_NAMES.workers,
    mobileQueueNum: 1,
    routes: [
      {
        path: '/workers/:workerId',
        component: WorkerPage,
        mobileQueueNum: 1,
      },
    ],
  },
  {
    path: '/managers/create',
    component: ManagerCreatePage,
    mobileQueueNum: 2,
  },
  {
    path: '/managers/edit/:managerId',
    component: ManagerEditPage,
    mobileQueueNum: 2,
  },
  {
    path: '/managers',
    label: 'Менеджеры',
    isShowMenu: false,
    component: ManagersPage,
    routeName: ROUTE_NAMES.managers,
    mobileQueueNum: 2,
    routes: [
      {
        path: '/managers/:managerId',
        component: ManagerPage,
        mobileQueueNum: 2,
      },
    ],
    icon: Icons.IconMenuManagers,
  },


  {
    path: '/events',
    label: 'События',
    isShowMenu: false,
    exact: true,
    component: EventData,
    icon: Icons.IconEvents,
    mobileQueueNum: 4,
  },

  {
    path: '/reports',
    label: 'Отчеты',
    isShowMenu: false,
    exact: true,
    component: ReportsPage,
    icon: Icons.IconMenuReports,
    routeName: ROUTE_NAMES.reports,
    mobileQueueNum: 8,
  },
  {
    path: '/log',
    label: 'Лог',
    isShowMenu: false,
    exact: true,
    component: LogDataPage,
    icon: Icons.IconMenuLog,
    routeName: ROUTE_NAMES.logs,
    mobileQueueNum: 7,
  },

  {
    path: '/access',
    label: 'Настройки доступа',
    isShowMenu: false,
    exact: true,
    component: AccessSettingsPage,
    icon: Icons.IconMenuSettings,
    mobileQueueNum: 6,
  },
]

/* eslint-disable */
export const RouteWithSubRoutes = (route: IRoute) => {
  return (
    <Route
      path={route.path}
      render={(props) => {
        return <ErrorBoundary FallbackComponent={ErrorFallback}>
          <PageTransition>
            {/*@ts-ignore*/}
            <route.component {...props} routes={route.routes}>
              {route.routes?.map((route, i) => (
                <RouteWithSubRoutes key={route.path + i} {...route} />
              ))}
            </route.component>
          </PageTransition>
        </ErrorBoundary>
      }
      }
    />
  )
}
