import {AxiosPromise, AxiosRequestConfig} from 'axios'
import {RefetchOptions} from 'axios-hooks'

/* eslint-disable-next-line */
export type TAxiosRefetch<TData = any> = (
  config?: AxiosRequestConfig | undefined,
  options?: RefetchOptions | undefined
) => AxiosPromise<TData>
