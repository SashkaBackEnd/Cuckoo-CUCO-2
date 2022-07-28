import React, { useRef, useState } from 'react'
import axios from 'axios'
import moment from 'moment'
import { BackToMain } from '@components/BackToMain'
import { ReportsForm } from '@components/ReportsForm'
import { errorHandler } from '@app/errors'
import { IReportsFormValues } from '@components/ReportsForm/ReportsForm'
import { Page, PageBody, toast } from '@app/theme'
import {
  Box,
  Heading,
  HStack, IconButton,
  Menu, MenuButton,
  MenuItem,
  MenuList,
} from '@chakra-ui/react'
import { ReportsList } from '@components/ReportsList'
import { saveAs } from 'save-as'
import { IReportsByManagers } from '@models/reports'
import { useWindowSize } from '@hooks/useWindowSize'
import { ReportsMobilePage } from '@pages/ReportsMobilePage'
import { Icons } from '@components/UI/iconComponents'
import { SubmitErrorHandler, SubmitHandler } from 'react-hook-form'


interface INormalizedData {
  to?: number,
  from?: number,
  type?: number | string
}


interface IRefCurrent {
  handleSubmit: (onValid: SubmitHandler<any>, onInvalid?: SubmitErrorHandler<IReportsFormValues>) => (e?: React.BaseSyntheticEvent) => Promise<void>
}

export const TYPES_FETCH = {
  1: 'guards',
  2: 'objects',
  3: 'managers',
}

export const ReportsPage: React.FC = () => {

  const [from, setFrom] = useState<number>(moment(new Date()).unix())
  const [to, setTo] = useState<number>(moment(new Date()).unix())
  const [type, setType] = useState<number | string>(1)
  const [fetchedReports, setFetchedReports] = useState<IReportsByManagers[]>([])
  const [isLoading, setIsLoading] = useState<boolean>(false)



  const submitHandler = (data: IReportsFormValues) => {

    const normalizedData: INormalizedData = {
      to: +moment(+data.to).unix(),
      from: +moment(+data.from).unix(),
      type: TYPES_FETCH[type],
    }

    axios.get(
      `/api/reports/excel/${normalizedData.from}/${normalizedData.to}/${normalizedData.type}`,
      { responseType: 'blob' },
    ).
      then(({ data }) => {
        toast({ title: 'Отчет успешно сформирован' })
        const blob = new Blob(
          [data], { type: 'application/vnd.ms-excel;charset=utf-8' })
        saveAs(blob, `entity_${from}_${to}.xls`)
      }).
      catch((error) => {
        errorHandler(error)
      })
  }

  const handleFetchReports = (data: IReportsFormValues) => {
    const normalizedData = {
      ...data,
      to: moment(data.to).unix(),
      from: moment(data.from).unix(),
      type: TYPES_FETCH[data.type]
    }

    const { to, from, type } = normalizedData


    setIsLoading(true)

    axios.get(`api/reports/${type}/${from}/${to}`).then((res) => {
      setFetchedReports(res.data)
      setIsLoading(false)
      console.log(Object.entries(TYPES_FETCH).find(el => el[1] == type)[0])
      setType(Object.entries(TYPES_FETCH).find(el => el[1] == type)[0])
    }).catch(err => errorHandler(err))

  }


  const { isMobile } = useWindowSize()

  const changeHandler = (value: IReportsFormValues) => {

    const normalizedData: INormalizedData = {

      type: value.type,
      to: +moment(value.to).unix(),
      from: +moment(value.from).unix(),
    }
    const { from, to, type } = normalizedData

    setFrom(from)
    setTo(to)
    setType(type)

  }

  const selectHandler = (value: any) => {
    const normalizedData: INormalizedData = {
      type: value.value,
    }
    const { type } = normalizedData
    setType(type)
  }



  let obj: IRefCurrent
  const formRef = useRef(obj)


  return (
    <div>
      <BackToMain />
      <Page>
        <PageBody bg="white" p={10}>
          <HStack justifyContent={'space-between'} alignItems={'flex-start'}>

            <Heading as="h4" size="lg" mb={6}>
              Отчеты
            </Heading>

            <Menu>
              <MenuButton
                isRound
                zIndex={1}
                size="sm"
                as={IconButton}
                aria-label="Опции"
                colorScheme="gray"
                icon={<Icons.IconDots />}
                variant="outline"
              />

              <MenuList zIndex={1000}>
                <MenuItem onClick={formRef.current?.handleSubmit(submitHandler)} type="button"
                  icon={<Icons.IconExport />}>
                  Экспорт в Excel
                </MenuItem>
              </MenuList>
            </Menu>


          </HStack>

          <ReportsForm changeHandler={changeHandler}
            selectHandler={selectHandler}
            submitHandler={handleFetchReports}
            isFetching={isLoading}
            ref={formRef}

          />
        </PageBody>
      </Page>
      <Page>
        <PageBody>
          {isMobile ?
            <ReportsMobilePage reports={fetchedReports} /> : <ReportsList type={type}
              reports={fetchedReports} />}
        </PageBody>
      </Page>
    </div>
  )
}

