class Search {

  constructor(config) {
    this.url = config.url
    this.method = config.method
    this.inputQuery = config.input || 'input[name=q]'
    this.container = document.getElementById(config.container) || false
    this.containerInitial = this.container.innerHTML

    this.init()
  }

  init() {
    let input = document.querySelector(this.inputQuery)

    input.addEventListener('keyup', () => {
      let q = input.value
      if (typeof this.Oq === 'undefined') {
        this.query(q)
        this.Oq = q
      } else {
        if (q !== this.Oq) {
          this.query(q)
        }
      }
    })
  }

  query(str) {
    if (str.length > 0) {
      xhr.request({
        url: this.url,
        method: this.method.toUpperCase(),
        data: {q: str},
        success: res => this.handleResponse(res)
      })
    } else {
      this.handleResponse()
    }
  }

  handleResponse(res = '') {
    if (res === '') {
      this.container.innerHTML = this.containerInitial
    } else {
      if (this.container.innerHTML !== res) {
        this.container.innerHTML = res
      }
    }
  }

}