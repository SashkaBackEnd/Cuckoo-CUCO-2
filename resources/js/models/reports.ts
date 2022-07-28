export interface IReportsArgs {
  fromDate: number,
  toDate: number
  type: string
}


// export interface IReportsByManagersShifts {
//   caseMissed: number,
//   caseObjectGuardMismatch: number,
//   caseShiftChange: number,
//   caseShiftTimeExceed: number,
//   caseShirtError: number,
//   id: number,
//   name: string,
//   salary: number,
//   shifts?: IReportsByManagersShifts,
//   totalCalls: number,
//   totalDoneShifts: number,
//   totalEmergencyCases: number,
//   totalErrors: number,
//   totalWorkHours: number,
// }


export interface IReportsByManagers {
  caseMissed: number
  caseObjectGuardMismatch: number
  caseShiftChange: number
  caseShiftTimeExceed: number
  caseShirtError: number
  id: number
  name: string
  salary: number
  totalShiftCount: number,
  shifts: IReportsByManagers[] | null,
  totalCalls: number
  totalDoneShifts: number
  totalEmergencyCases: number
  totalErrors: number
  totalWorkHours: number
  object_name?: string
  shortName?: string
  startTime: number
  endTime: number
  totalWorkHoursString: string,
  fullName?: string
}
