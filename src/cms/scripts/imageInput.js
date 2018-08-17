let imageInputs = document.querySelectorAll('.file__image')
if (imageInputs) {
  imageInputs.forEach(input => {
    let label = input.querySelector('.file__label')
    let labelValue = label.innerHTML
    let fileInput = input.querySelector('input[type=file]')
    fileInput.addEventListener('change', event => {
      let fileName = ''
      if (fileInput.files && fileInput.files.length > 1) {
        fileName = `${fileInput.files.length} files selected`
      } else {
        fileName = event.target.value.split('\\').pop()
      }
      if (fileName) {
        label.innerHTML = fileName
      } else {
        label.innerHTML = labelValue
      }
    })
  })
}