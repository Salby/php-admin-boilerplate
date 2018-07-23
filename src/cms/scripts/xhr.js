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

'use strict';

var xhr = {
  /**
   * Sends data with XHR
   *
   * @param {string} method - The method you want to use to send data, either GET or POST
   * @param {string} url - The url you want to send data to
   * @param {string/object} data - The data you want to send
   * @param {function} success - Lets you work with the response in a function
   */
  /*request: function(method, url, data, success) {
      var GET = method.toUpperCase() === 'GET';
      var POST = method.toUpperCase() === 'POST';
      var params = typeof data == 'string' ? data : Object.keys(data).map(function (k) {
          return encodeURIComponent(k) + '=' + encodeURIComponent(data[k]);
      }).join('&');
      var request = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
      var u = GET ? url + '?' + params : url;
      if (GET || POST) var m = method.toUpperCase();
      request.open(m, u);
      request.onreadystatechange = function () {
          if (request.readyState > 3 && request.status === 200) success(request.responseText);
      };
      request.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
      if (POST) {
          request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
          request.send(params);
      } else {
          request.send();
      }
  }*/
  request: obj => {
    obj.data = obj.data || {};

    let GET = obj.method.toUpperCase() === 'GET';
    let POST = obj.method.toUpperCase() === 'POST';
    let params = typeof obj.data == 'string' ? obj.data : Object.keys(obj.data).map(key => {
      return encodeURIComponent(key) + '=' + encodeURIComponent(data[k]);
    }).join('&');
    let request = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    let u = GET ? obj.url + '?' + params : obj.url;
    if (GET || POST) var m = obj.method.toUpperCase();
    request.open(m, u);
    request.onreadystatechange = () => {
      if (request.readyState > 3 && request.status === 200) obj.success(request.responseText);
    };
    request.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    if (POST) {
      request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
      request.send(params);
    } else {
      request.send();
    }
  }
};