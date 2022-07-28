import React, {Component} from 'react'
import classes from './CheckStatus.module.css'
import Button from '../UI/Button/Button'
import Select from 'react-select'
import {connect} from 'react-redux'
import {fetchGuards} from '../../store/actions/guards'
import Loader from '../UI/Loader/Loader'
import {checkObjectStatus, guardToObjectControl, stopCheckCalls} from '../../store/actions/objects'
import {timeConverter} from '../../timeConverter'
import {substringOut} from '../../substringOut'

class CheckStatus extends Component {
  state = {
    showSetGuard: false,
    selectedOption: null,
    isDisabled: false
  }

  componentDidMount() {
    this.props.fetchGuards()
    // this.props.stopCheckCalls()
  }

  guardToObjectControlHandler = (objectId, guardId, type, update) => {
    this.props.guardToObjectControl(objectId, guardId, type, update)
    // this.setState({
    //     showSetGuard: false
    // })
  }

  // startPostHandler = () => {
  //     this.setState({
  //         showSetGuard: !this.state.showSetGuard
  //     })
  //
  //     if (!this.state.showSetGuard) {
  //         this.props.fetchGuards()
  //     }
  // }

  onChangeSelectHandler = (selectedOption) => {
    this.setState({
      selectedOption
    })
  }

  checkObjectStatusHandler = (objectId, type, guardId) => {
    this.setState({
      isDisabled: true
    })
    this.props.checkObjectStatus(objectId, type, guardId)
  }

  render() {
    let option
    const options = []
    this.props.guards.map((guard, index) => {
      if (!guard.currentObject && guard.active === 1) {
        option = {
          value: guard.id,
          label: guard.surname + ' ' + guard.name + ' ' + guard.patronymic
        }
        options.push(option)
      }
    })

    let img = <img src="/images/svg/icon-home.svg" alt="" />

    let text

    if (!this.props.lastCheck && this.props.currentShifts && this.props.currentShifts.length !== 0) {
      img = <img src="/images/svg/icon-success.svg" alt="" />
    }

    if (this.props.lastCheck && this.props.lastCheck.type === 'good') {
      text = 'Успешная проверка'
      img = <img src="/images/svg/icon-success.svg" alt="" />
    }

    if (this.props.lastCheck && this.props.lastCheck.type === 'bad') {
      text = 'Неудачная проверка'
      img = <img src="/images/svg/icon-danger.svg" alt="" />
    }

    if (this.props.lastCheck && this.props.lastCheck.type === 'sos') {
      text = 'На посту тревога'
      img = <img src="/images/svg/icon-danger.svg" alt="" />
    }

    const {selectedOption} = this.state

    let selectBlock = <Loader />

    let status = (
      <React.Fragment>
        <div className={classes.CheckStatus}>
          <div>
            {img}
            <div>
              <p>Охрана не</p>
              <p>назначена</p>
            </div>
          </div>
        </div>
      </React.Fragment>
    )

    selectBlock = (
      <div className={classes.setGuard}>
        <hr />
        <p>Выберите охранника для поста</p>
        <div className={classes.selectBlock}>
          <Select
            value={selectedOption}
            onChange={this.onChangeSelectHandler}
            className={classes.select}
            isSearchable={true}
            options={options}
            placeholder="Начните вводить ФИО охранника"
          />
          <Button
            type="success"
            onClick={() =>
              this.guardToObjectControlHandler(this.props.objectId, this.state.selectedOption.value, 'start', 'object')
            }
          >
            Добавить охранника
          </Button>
        </div>
      </div>
    )

    // if (this.props.lastCheck) {
    //
    //     if (this.props.type === 'guard') {
    //
    //         status = (
    //             <div className={classes.CheckStatus}>
    //                 <div>
    //                     {img}
    //                     <div>
    //                         <p>{text}</p>
    //                         <p>{timeConverter(this.props.lastCheck.date, 'words')}</p>
    //                     </div>
    //                 </div>
    //                 {
    //                     this.props.lastCheck.type === 'sos'
    //                         ? <Button disabled={this.state.isDisabled}
    //                                   onClick={() => this.checkObjectStatusHandler(this.props.currentShift.guardedObject.id, 'sos', this.props.guardId)}
    //                                   type='CheckStatus'>Отключить</Button>
    //                         : <Button disabled={this.state.isDisabled}
    //                                   onClick={() => this.checkObjectStatusHandler(this.props.currentShift.guardedObject.id, this.props.type, this.props.guardId)}
    //                                   type='CheckStatus'>Проверить</Button>
    //                 }
    //             </div>
    //         )
    //     }

    // if (this.props.currentShifts) {
    //     status = (
    //         <div className={classes.CheckStatus}>
    //             <div>
    //                 {img}
    //                 <div>
    //                     <p> {this.props.currentShift.status === 'finishing'
    //                         ? 'Охранник сейчас завершает смену на объекте '
    //                         : text
    //                     }</p>
    //                     <p>{timeConverter(this.props.lastCheck.date, 'words')}</p>
    //                 </div>
    //             </div>
    //             {
    //                 this.props.currentShift.status === 'finishing'
    //                     ? null
    //                     : <Button disabled={this.state.isDisabled}
    //                               onClick={() => this.checkObjectStatusHandler(this.props.currentShift.guardedObject.id, this.props.type, this.props.guardId)}
    //                               type='CheckStatus'>Проверить</Button>
    //             }
    //         </div>
    //     )
    // }

    if (this.props.lastCheck && this.props.lastCheck.type) {
      status = (
        <div className={classes.CheckStatus}>
          <div>
            {img}
            <div>
              <p>{text}</p>
              <p>{timeConverter(this.props.lastCheck.date, 'words')}</p>
            </div>
          </div>
        </div>
      )
    }

    if (this.props.lastCheck && this.props.lastCheck.type === 'sos') {
      status = (
        <div className={classes.CheckStatus}>
          <div>
            {img}
            <div>
              <p>{text}</p>
              <p>{timeConverter(this.props.lastCheck.date, 'words')}</p>
            </div>
          </div>
          {this.props.lastCheck.type === 'sos' ? (
            <Button
              disabled={this.state.isDisabled}
              onClick={() => this.checkObjectStatusHandler(this.props.objectId, 'sos')}
              type="CheckStatus"
            >
              Отключить
            </Button>
          ) : (
            <Button
              disabled={this.state.isDisabled}
              onClick={() => this.checkObjectStatusHandler(this.props.objectId)}
              type="CheckStatus"
            >
              Проверить
            </Button>
          )}
        </div>
      )
    }

    // } else {

    // )

    if (!this.props.lastCheck && this.props.currentShifts) {
      status = (
        <div className={classes.CheckStatus}>
          <div>
            {img}
            <div>
              <p>Охрана на</p>
              <p>посту</p>
            </div>
          </div>
          <Button
            disabled={this.state.isDisabled}
            onClick={() => this.checkObjectStatusHandler(this.props.objectId)}
            type="CheckStatus"
          >
            Проверить
          </Button>
        </div>
      )
    }

    return (
      <React.Fragment>
        {status}
        {selectBlock}
      </React.Fragment>
    )
  }
}

function mapStateToProps(state) {
  return {
    guards: state.guards.guards,
    loading: state.guards.loading
  }
}

function mapDispatchToProps(dispatch) {
  return {
    fetchGuards: () => dispatch(fetchGuards()),
    guardToObjectControl: (objectId, guardId, type, update) =>
      dispatch(guardToObjectControl(objectId, guardId, type, update)),
    checkObjectStatus: (objectId, type, guardId) => dispatch(checkObjectStatus(objectId, type, guardId))
    // stopCheckCalls: () => dispatch(stopCheckCalls())
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(CheckStatus)
