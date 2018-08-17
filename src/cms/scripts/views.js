class Views {
  constructor(containerId, config) {
    config = config || {}
    this.linkQuery = config.linkQuery || '.views'

    this.container = document.getElementById(containerId)

    // Find all links that match query and initialize.
    this.links = document.querySelectorAll(this.linkQuery) // Find links matching query.
    this.links[0].classList.add('active')
    this.links.forEach(link => {
      if (!link.initialized) { // If link isn't initialized.
        this.viewsLink(link) // Initialize link.
      }
    })
  }

  viewsLink(link) {

    link.addEventListener('click', event => {
      event.preventDefault()
      this.loadView(link.href)
      activeElem(link, this.links)
    })

    // Mark link as initialized.
    link.initialized = true
  }

  loadView(url, config) {
    config = config || {}

    xhr.request({
      method: 'GET',
      url: url,
      success: res => {
        this.container.innerHTML = res
      }
    })
  }
}

const activeElem = (target, list, className = 'active') => {
  list.forEach(elem => {
    if (elem === target) {
      if (!target.classList.contains(className)) target.classList.add(className)
    } else {
      if (elem.classList.contains(className)) elem.classList.remove(className)
    }
  })
}