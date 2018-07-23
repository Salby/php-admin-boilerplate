class Dialog {

  constructor(config = {}) {

    this.title = config.title || null;
    this.body = config.body;
    this.actions = config.actions || {
      close: 'Dialog.close(this)'
    };
  }

  static open(elem) {

    let dialog = findDialog(elem); // Find dialog.

    if (dialog) { // If dialog is found.
      dialog.classList.add('active'); // Add active className that contains opening animations.
      document.body.style.overflowY = 'none'; // Disable scroll.
    }
  }

  static close(elem) {

    let dialog = findDialog(elem); // Find dialog.

    if (dialog.classList.contains('active')) { // Check if dialog is active.
      dialog.classList.remove('active'); // Remove active className.
      dialog.classList.add('closing'); // Add closing className that contains closing animations.
      dialog.addEventListener('animationend', () => dialog.remove()); // Remove dialog node when closing animation has finished.
      document.body.removeAttribute('style'); // Enable again.
    }
  }

}

const findDialog = elem => elem.classList.contains('dialog')
  ? elem
  : elem.closest('.dialog');