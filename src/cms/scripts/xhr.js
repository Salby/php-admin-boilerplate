/*
MIT License
Copyright (c) 2018 Sander Larsen
Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:
The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.
THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/

class xhr {
  static config(obj) {
    let data,
      get,
      post,
      url,
      method

    if (obj.data) {
      data = obj.data === 'string'
        ? obj.data
        : Object.keys(obj.data).map(key => {
          return encodeURIComponent(key) + '=' + encodeURIComponent(obj.data[key])
        }).join('&')
    } else {
      data = ''
    }

    if (obj.method) {
      get = obj.method.toUpperCase() === 'GET'
      post = obj.method.toUpperCase() === 'POST'
    } else {
      get = true
      post = false
    }

    if (obj.url) {
      url = get
        ? obj.url + '?' + data
        : obj.url
    }

    method = get || post
      ? obj.method.toUpperCase()
      : 'GET'

    return {
      url: url,
      method: method,
      params: data
    }
  }

  static send(obj) {

    const config = xhr.config(obj)

    let request = window.XMLHttpRequest
      ? new XMLHttpRequest()
      : new ActiveXObject('Microsoft.XMLHTTP')

    request.open(config.method, config.url)

    request.onreadystatechange = () => {
      if (request.readyState > 3 && request.status === 200)
        obj.success(request.responseText)
    }

    request.setRequestHeader('X-Requested-With', 'XMLHttpRequest')

    if (config.method === 'POST') {
      request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded')
      request.send(config.params)
    } else {
      request.send()
    }
  }

  static request(obj) {
    return new Promise((resolve, reject) => {

      const config = xhr.config(obj)

      let request = window.XMLHttpRequest
        ? new XMLHttpRequest()
        : new ActiveXObject('Microsoft.XMLHTTP')

      request.open(config.method, config.url)

      request.onload = () => {
        if (request.status >= 200 && request.status < 300) {
          resolve(request.response)
        } else {
          reject(request.statusText)
        }
      }

      request.setRequestHeader('X-Requested-With', 'XMLHttpRequest')

      if (config.method === 'POST') {
        request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded')
        request.send(config.params)
      } else {
        request.send()
      }
    })

  }
}
