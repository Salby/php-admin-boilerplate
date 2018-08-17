class Form {

  constructor(formId, config) {
    config = config || {}
    this.validate = config.validate || true
    this.customStyles = config.customStyles || false
    this.regex = config.regex || {
      name: /^(([A-Za-z]+[\-\']?)*([A-Za-z]+)?\s)+([A-Za-z]+[\-\']?)*([A-Za-z]+)?$/,
      email: /^(([^<>()\[\]\.,;:\s@\"]+(\.[^<>()\[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i,
    }
    this.errors = config.errors || {
      errorEmpty: 'Field is empty',
      errorIncorrect: 'Input is incorrect'
    }

    this.types = [
      'text',
      'email',
      'password',
      'number',
      'datetime',
      'tel',
      'file'
    ]

    this.form = document.getElementById(formId)
    this.inputs = this.form.querySelectorAll('input, textarea, select')
    this.initForm()
    this.form.addEventListener('submit', (e) => {
      e.preventDefault()
      this.validateForm()
    })
  }

  initForm() {
    for (let i = 0; i < this.inputs.length; i++) {
      let input = this.inputs[i]
      let type = this.inputs[i].type
      if (input.tagName === 'INPUT') {
        if (this.types.find(el => el === type)) {
          this.initField(this.inputs[i])
        }
      } else if (input.tagName === 'TEXTAREA') {
        this.initField(this.inputs[i])
        input.addEventListener('input', () => scaleTextArea(input))
        input.addEventListener('change', () => scaleTextArea(input))
      }
    }
  }

  initField(field) {
    field.container = field.parentNode
    if (!this.customStyles) {
      field.label = findSibling(field, 'label')
    }
    if (!this.customStyles && field.value.length > 0) {
      field.label.classList.add('hovering')
    }

    if (!this.customStyles) {
      field.updateState = () => {
        const inputLength = field.value.length
        if (field.label && !this.customStyles) {
          if (inputLength > 0) {
            field.label.classList.add('hovering')
          } else {
            field.label.classList.remove('hovering')
          }
        }
      }
    }

    if (!this.customStyles) {
      field.addEventListener('focusin', () => {
        if (field.label) {
          field.label.classList.add('hovering')
          field.label.classList.add('focus')
        }
      })
    }
    field.addEventListener('focusout', () => {
      if (field.label && !this.customStyles) {
        field.label.classList.remove('focus')
      }
      field.updateState()
      if (this.validate && field.required) {
        this.validateField(field, false)
      }
    })
    field.addEventListener('change', () => field.updateState())

  }

  validateForm() {
    for (let i = 0; i < this.inputs.length; i++) {
      let input = this.inputs[i]
      let type = input.type
      if (input.required) {
        if (this.types.find(el => el === type)) {
          if (!this.validateField(input)) return false
        } else if (input.tagName === 'TEXTAREA') {
          if (!this.validateField(input)) return false
        }
      }
    }
    this.form.submit()
  }

  validateField(field, complete = true) {
    let type = field.dataset.validateType
    if (field.value.length === 0 && complete) {
      let message = this.errors['errorEmpty']
      if (field.dataset.errorEmpty) {
        message = field.dataset.errorEmpty
      }
      this.fieldError(field, message)
      return false
    } else if (type) {
      if (type) {
        let toValidate = field.value
        let regex = this.regex[type]
        if (regex.exec(toValidate)) {
          return true
        } else {
          let message = this.errors['errorIncorrect']
          if (field.dataset.errorIncorrect) {
            message = field.dataset.errorIncorrect
          }
          this.fieldError(field, message)
        }
      }
    } else {
      return true
    }
  }

  fieldError(field, message) {
    //if (!field.container.classList.contains('error')) field.container.classList.add('error')
    if (field.container && field.container.classList.contains('error')) {
      field.container.classList.add('error')
    }
    if (!findSibling(field, 'small')) {
      field.messageContainer = document.createElement('small')
      field.messageContainer.innerText = message
      field.container.appendChild(field.messageContainer)
    }
    field.addEventListener('keydown', () => {
      field.container.classList.remove('error')
      field.messageContainer.remove()
    })
  }
}

function findSibling(referenceElem, query) {
  let parent = referenceElem.parentNode
  return parent.querySelector(query)
}

function scaleTextArea(field) {
  field.style.height = 'inherit'
  var computed = window.getComputedStyle(field)
  /*let height = parseInt(computed.getPropertyValue('border-top-width'), 10)
  + parseInt(computed.getPropertyValue('padding-top'), 10)
  + field.scrollHeight
  + parseInt(computed.getPropertyValue('padding-bottom'), 10)
  + parseInt(computed.getPropertyValue('border-bottom-width'), 10)*/
  let height = field.scrollHeight + 2
  /*+ parseInt(computed.getPropertyValue('padding-top'), 12)
  + parseInt(computed.getPropertyValue('padding-bottom'), 12)*/
  field.style.height = height + 'px'
}