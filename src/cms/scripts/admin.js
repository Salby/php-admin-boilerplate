// Drawer.
let drawerOpen = document.getElementById('drawer-open')

if (drawerOpen) {
  let drawer = document.getElementById(drawerOpen.dataset.drawer)

  drawer.show = () => {
    drawer.classList.add('active')
  }
  drawer.hide = () => {
    drawer.classList.remove('active')
  }

  drawerOpen.addEventListener('click', () => {
    if (!drawer.classList.contains('active')) {
      drawer.show()
    }
  })
  window.addEventListener('click', event => {
    if (!event.target.closest('.drawer') && !event.target.closest('#drawer-open'))
      if (drawer.classList.contains('active'))
        drawer.hide()
  })
}

// Set theme-color.
let appbar = document.querySelector('.appbar')

const rgbToHex = (r, g, b) => '#' + [r, g, b].map(x => {
  const hex = parseInt(x).toString(16)
  return hex.length === 1 ? '0' + hex : hex
}).join('')

if (appbar) {
  let themeColor = window.getComputedStyle(appbar, null).getPropertyValue('background-color')
  themeColor = themeColor.substring(4, themeColor.length - 1)
    .replace(/ /g, '')
    .split(',')
  themeColor = rgbToHex(themeColor[0], themeColor[1], themeColor[2])
  let meta = document.createElement('meta')
  meta.name = 'theme-color'
  meta.content = themeColor
  document.getElementsByTagName('head')[0].appendChild(meta)
}