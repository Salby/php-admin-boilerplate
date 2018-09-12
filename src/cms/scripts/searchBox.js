class SearchBox {
  constructor(query, config) {
    // Set config.
    config = config || {}
    this.placeholder = config.placeholder || 'Search'
    this.createState = config.createState ||  "<div class='search-box__empty'>" +
                                              "No results found." +
                                              "<button type='button' class='button__flat--primary' id='add-new'>" +
                                                "Add new" +
                                              "</button>" +
                                            "</div>"
    this.emptyState = config.emptyState || "<div class='search-box__empty'>No results found.</div>"

    // Get results from query.
    this.nodeList = document.querySelectorAll(query)

    if (this.nodeList.length) {
      this.className = this.nodeList[0].classList[0]
      // Iterate and initialize searchboxes.
      this.nodeList.forEach(searchBox => {
        if (!searchBox.initialized) {
          this.init(searchBox)
          // Mark as initialized.
          searchBox.initialized = true
        }
      })
    }
  }

  init(elem) {
    // Dummy input.
    elem.input = elem.querySelector('.input')
    this.event = new Event('input-changed')
    elem.input.addEventListener('input-changed', () => this.inputState(elem.input))
    this.inputState(elem.input)

    // Actual input.
    elem.hiddenInput = elem.querySelector('input[type=hidden]')

    // Searchbox box.
    elem.box = elem.querySelector('.search-box')
    elem.list = elem.querySelector('.search-box__container')

    elem.box.enter = event => {
      if (event.key === 'Enter') {
        let addNew = elem.list.querySelector('button')
        let listItem = elem.list.querySelector('li')

        if (addNew || listItem) {
          if (addNew)
            addNew.click()
          else if (listItem)
            listItem.click()
          event.preventDefault()
        }
      }
    }

    // Set user-add bool.
    elem.userAdd = elem.box.dataset.userAdd === 'true'
    elem.box.removeAttribute('data-user-add')

    // JSON.
    elem.JSON = elem.box.getAttribute('data-list')
    // Remove data-list attribute.
    elem.box.removeAttribute('data-list')
    elem.boxClosedEvent = new Event('box-closed')
    elem.box.addEventListener('box-closed', () => {

    })

    // Insert list function.
    elem.insertList = (indexes = []) => {
      // Create new list.
      let list = SearchList.list(elem.JSON, indexes)
      // Remove content from list container.
      elem.list.innerHTML = ''
      // Insert list into container.
      elem.list.appendChild(list)
      // Find list items and initialize.
      let listItems = list.querySelectorAll('li')
      listItems.forEach(li => {
        li.addEventListener('click', event => {
          event.stopPropagation()
          const selected = li.dataset.value
          const label = li.innerHTML
          this.updateInput(elem.hiddenInput, selected)
          this.updateDummy(elem.input, label)
          this.close(elem.box)
        })
      })
    }

    // Initial list.
    elem.insertList()

    // Search.
    elem.search = elem.querySelector('.search-box__input')
    // Set placeholder.
    elem.search.placeholder = this.placeholder
    // Search functionality.
    elem.search.addEventListener('keyup', () => {
      const q = elem.search.value

      if (q.length) {
        // Parse JSON.
        const source = JSON.parse(elem.JSON)
        // Get indexes from JSON search.
        let result = SearchJSON.match(source, q, Object.keys(source[0])[1])
        result = result.indexes

        if (!result.length) {
          if (elem.userAdd) {
            // Repalce search-box content.
            elem.list.innerHTML = this.createState

            let addNew = document.querySelector('#add-new')
            // Update hidden value and dummy input.
            addNew.addEventListener('click', () => {
              // Add destyled value to hidden input.
              const toSave = q.deStyle()
              this.updateInput(elem.hiddenInput, toSave)
              // Add styled value to dummy input.
              const toDisplay = q.style()
              this.updateDummy(elem.input, toDisplay)
              // Close search-box.
              this.close(elem.box)
            })
          } else {
            // Replace search-box content.
            elem.list.innerHTML = this.emptyState
          }

        } else {
          // Replace search-box content with list from query results.
          elem.insertList(result)
        }
      } else {
        // Replace search-box content.
        elem.insertList()
      }

      // Listen for enter presses, and "click" target.
      window.addEventListener('keydown', event => { elem.box.enter(event) })
    })

    // Open & close.
    elem.input.addEventListener('click', () => {
      if (!elem.box.classList.contains('open')) {
        this.open(elem.box)
        let vw = Math.max(document.documentElement.clientWidth, window.innerWidth || 0)
        if (vw > 480)
          elem.search.focus()
      }
    })
    window.addEventListener('click', event => {
      if (!event.target.closest(`.${this.className}`))
        this.nodeList.forEach(searchBox => {
          this.close(searchBox.box)
        })
    })
  }

  updateInput(input, value) {
    input.value = value
  }
  updateDummy(dummy, text) {
    dummy.innerText = text
    dummy.dispatchEvent(this.event)
    this.inputState(dummy)
  }

  inputState(input) {
    let label = input.querySibling('.label')
    const empty = input.innerHTML.length === 0
    if (!empty) {
      label.classList.add('hovering')
    } else {
      label.classList.remove('hovering')
    }
  }

  open(box) {
    box.classList.add('open')
  }
  close(box) {
    window.removeEventListener('keydown', event => { box.enter(event) })
    box.classList.replace('open', 'closing')
    box.addEventListener('animationend', () => {
      box.classList.remove('closing')
    })
  }
}

// Search box list class.
class SearchList {
  static list(json, indexes = []) {
    // Parse JSON.
    const parsedJSON = JSON.parse(json)
    // Create list element.
    let list = document.createElement('ul')
    if (!indexes.length) {
      // Iterate over parsed JSON if no indexes are given.
      parsedJSON.forEach(obj => {
        // Add item to list.
        list.appendChild(this.item(obj))
      })
    } else {
      // Iterate over indexes in parsed JSON.
      indexes.forEach(index => {
        // Add item to list.
        list.appendChild(SearchList.item(parsedJSON[index]))
      })
    }
    // Return finished list.
    return list
  }

  static item(obj) {
    // Parse object as array.
    const data = Object.values(obj)
    // Create list item element.
    let item = document.createElement('li')
    // Set item content.
    item.innerText = style(`${data[1]}`)
    // Set item data-value.
    item.setAttribute('data-value', data[0])
    // Return list item.
    return item
  }
}

String.prototype.capitalize = function() {
  return this.charAt(0).toUpperCase() + this.slice(1)
}
Object.prototype.querySibling = function(query) {
  let parent = this.parentNode
  return parent.querySelector(query)
}

String.prototype.style = function() {
  return this.replace('_', ' ').capitalize()
}
String.prototype.deStyle = function() {
  return this.replace(' ', '_').toLowerCase()
}
const style = string => string.replace('_', ' ').capitalize()
const deStyle = string => string.replace(' ', '_').toLowerCase()

new SearchBox('.form__group--searchbox, .searchbox')