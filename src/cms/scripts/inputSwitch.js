class inputSwitch {

  constructor(containerId) {

    this.container = document.getElementById(containerId)

    this.switcher = document.getElementById(this.container.dataset.switch)
    this.currentIndex = 0

    this.inputs = this.container.querySelectorAll('[data-switch-index]')

    this.init()

  }

  init() {

    this.inputs.forEach(inputContainer => {
      inputContainer.style.display = 'none'
    })

    this.switch()

    let list = document.querySelector('.select__list')
    list.addEventListener('click', () => {
      this.currentIndex = this.switcher.selectedIndex
      this.switch()
    })

  }

  switch() {

    this.inputs.forEach(inputContainer => {
      if (parseInt(inputContainer.dataset.switchIndex) === this.currentIndex) {
        inputContainer.removeAttribute('style')
      } else {
        inputContainer.style.display = 'none'
      }
    })

  }

}