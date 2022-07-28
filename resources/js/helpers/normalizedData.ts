import { unmaskPhone } from '@app/helpers/unmaskPhone'
import { mergeDatesToUnix } from '@components/PostForm/PostForm'
import moment from 'moment'


export const normalizeData = (data) => ({
  ...data,
  phone: unmaskPhone(data.phone),
  Mon: {  salary: data.Mon.salary || ""  , times: mergeDatesToUnix(data.Mon.times)},
  Thu:  {...data.Thu, salary: data.Thu.salary || "",   times: mergeDatesToUnix(data.Thu.times)},
  Wed:  {...data.Wed, salary: data.Wed.salary || "",   times: mergeDatesToUnix(data.Wed.times)},
  Tue:  {...data.Tue, salary: data.Tue.salary || "",  times: mergeDatesToUnix(data.Tue.times)},
  Fri:  {...data.Fri, salary: data.Fri.salary || "",   times: mergeDatesToUnix(data.Fri.times)},
  Sat:  {...data.Sat, salary: data.Sat.salary || "",   times: mergeDatesToUnix(data.Sat.times)},
  Sun:  {...data.Sun, salary: data.Sun.salary || "",   times: mergeDatesToUnix(data.Sun.times)},
  nonStandardWork: [
    ...data.nonStandardWork.map((work) => ({
      ...work,
      day: moment(work.day).unix(),
      times: mergeDatesToUnix(work.times),
    })),
  ],
})
