class Menu {
  constructor(query) {
    this.query = query
    this.menus = document.querySelectorAll(this.query)
    this.menus.forEach((menu) => {
      if (!menu.initialized) {
        this.initMenu(menu)
      }
    })
  }

  initMenu(elem) {
    elem.button = elem.querySelector('button')
    elem.list = elem.querySelector('ul')

    elem.setAttribute('data-collapsed', 'true')
    elem.list.style.width = 0 + 'px'
    elem.list.style.height = 0 + 'px'

    elem.show = function() {
      elem.classList.add('active')

      let width = elem.list.scrollWidth
      if (width < 112) {
        width = 112
      } else if (width > 280) {
        width = 280
      }

      let height = elem.list.scrollHeight

      elem.list.style.width = width + 'px'
      elem.list.style.height = height + 'px'

      elem.list.addEventListener('transitionend', (event) => {
        elem.list.removeEventListener('transitionend', event.callee)
      })

      this.setAttribute('data-collapsed', 'false')
    }
    elem.hide = function() {
      elem.classList.remove('active')

      let width = elem.list.scrollWidth
      let height = elem.list.scrollHeight
      let transition = elem.list.style.transition
      elem.list.style.transition = ''

      setTimeout(() => {
        elem.list.style.width = 0 + 'px'
        elem.list.style.height = 0 + 'px'
      }, 300)


      requestAnimationFrame(() => {
        elem.list.style.width = width + 'px'
        elem.list.style.height = height + 'px'
        elem.list.style.transition = transition

        requestAnimationFrame(() => {
          width = width - 16
          height = height - 16
          elem.list.style.width = width + 'px'
          elem.list.style.height = height + 'px'
        })
      })

      elem.setAttribute('data-collapsed', 'true')
    }

    elem.button.addEventListener('click', (event) => {
      event.stopPropagation()
      this.closeAll(elem)
      //elem.show()
      if (collapsed(elem)) {
        elem.show()
      } else {
        elem.hide()
      }
    })

    window.addEventListener('click', () => this.closeAll())

    elem.initialized = true
  }

  closeAll(elem) {
    let menus = document.querySelectorAll(this.query)
    let no = []
    let lists = []
    for (let i = 0; i < menus.length; i++) {
      lists.push(menus[i].list)
      if (elem === menus[i]) {
        no.push(i)
      }
    }
    for (let i = 0; i < lists.length; i++) {
      if (no.indexOf(i) && !collapsed(lists[i].parentNode)) {
        lists[i].parentNode.hide()
      }
    }
  }
}

const collapsed = (elem) => {
  return elem.getAttribute('data-collapsed') === 'true'
}

new Menu('*[class^=menu]')