import React, {Component} from 'react'
import classes from './CheckPost.module.css'
import Button from '../UI/Button/Button'
import {connect} from 'react-redux'
import {guardToObjectControl} from '../../store/actions/objects'
import {timeConverter} from '../../timeConverter'
import {substringOut} from '../../substringOut'
import {TransitionGroup} from 'react-transition-group'
import {Fade} from 'react-reveal'

class CheckPost extends Component {
  state = {
    isShown: false
  }

  setIsShown = (bool) => {
    this.setState({
      isShown: bool
    })
  }

  guardToObjectControlHandler = (objectId, guardId, type, update) => {
    this.props.guardToObjectControl(objectId, guardId, type, update)
  }

  render() {
    let block

    if (this.props.type === 'object') {
      block = (
        <div
          onMouseEnter={() => this.setIsShown(true)}
          onMouseLeave={() => this.setIsShown(false)}
          className={classes.CheckPost}
        >
          <div className={classes.body}>
            <div className={classes.info}>
              <p>
                <a>{this.props.currentShift.securityGuard.name}</a>
                <span> | PIN {this.props.currentShift.securityGuard.pin}</span>
              </p>
              <p className={classes.phone}>{this.props.currentShift.securityGuard.phone}</p>
            </div>
            {this.state.isShown ? (
              <TransitionGroup appear={true} enter={true} exit={true}>
                <Fade>
                  <Button
                    onClick={() =>
                      this.guardToObjectControlHandler(
                        this.props.objectId,
                        this.props.currentShift.securityGuard.id,
                        'end',
                        'object'
                      )
                    }
                  >
                    Завершить смену
                  </Button>
                </Fade>
              </TransitionGroup>
            ) : (
              <div className={classes.status}>
                <p>Заступил на пост</p>
                <p>{timeConverter(this.props.currentShift.startDate, 'words')}</p>
              </div>
            )}
          </div>
        </div>
      )
    } else {
      block = (
        <div
          onMouseEnter={() => this.setIsShown(true)}
          onMouseLeave={() => this.setIsShown(false)}
          className={classes.CheckPost}
        >
          <p className={classes.header}>
            {this.props.currentShift.status === 'finishing'
              ? 'Охранник сейчас завершает смену на посту'
              : 'Охранник сейчас на посту'}
          </p>
          <div className={classes.body}>
            <div className={classes.info}>
              <p>
                <a>{substringOut(this.props.currentShift.guardedObject.name, 30)}</a>
              </p>
              <p className={classes.phone}>{this.props.phone}</p>
            </div>
            {this.state.isShown ? (
              <TransitionGroup appear={true} enter={true} exit={true}>
                <Fade>
                  <Button
                    onClick={() =>
                      this.guardToObjectControlHandler(this.props.currentShift.id, this.props.guardId, 'end', 'guard')
                    }
                  >
                    Завершить смену
                  </Button>
                </Fade>
              </TransitionGroup>
            ) : (
              <div className={classes.status}>
                <p>Заступил на пост</p>
                <p>{timeConverter(this.props.currentShift.startDate, 'words')}</p>
              </div>
            )}
          </div>
        </div>
      )
    }
    return block
  }
}

function mapDispatchToProps(dispatch) {
  return {
    guardToObjectControl: (objectId, guardId, type, update) =>
      dispatch(guardToObjectControl(objectId, guardId, type, update))
  }
}

export default connect(null, mapDispatchToProps)(CheckPost)
