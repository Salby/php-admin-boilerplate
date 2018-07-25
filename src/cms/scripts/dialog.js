class Dialog {

  constructor(config = {}) {

    this.title = config.title || false;
    this.body = config.body;
    this.actions = config.actions || {
      close: 'Dialog.close(this)'
    };

    this.build();
  }

  build() {

    let dialog = document.createElement('div');
    dialog.classList.add('dialog');

    let actions = "<div class='dialog__actions'>";
    Object.keys(this.actions).forEach((name) => {
      actions += `<button type="button" class="button__flat--primary" onclick="${this.actions[name]}">${name}</button>`;
    });
    actions += "</div>";

    let title = this.title
      ? `<h1 class="dialog__title">${this.title}</h1>`
      : '';

    dialog.innerHTML = `
      <div class="dialog__card">
        ${title}
        <p class="dialog__body">${this.body}</p>
        ${actions}
      </div>
    `;

    document.body.appendChild(dialog);
    this.dialog = dialog;
  }

  static open(elem) {

    let dialog = findDialog(elem); // Find dialog.

    if (dialog) { // If dialog is found.
      dialog.classList.add('active'); // Add active className that contains opening animations.
      document.body.style.overflowY = 'none'; // Disable scroll.
    }
  }

  static close(elem, removeNode = false) {

    let dialog = findDialog(elem); // Find dialog.

    if (dialog.classList.contains('active')) { // Check if dialog is active.
      dialog.classList.remove('active'); // Remove active className.
      dialog.classList.add('closing'); // Add closing className that contains closing animations.
      dialog.addEventListener('animationend', () => {
        if (removeNode) {
          dialog.remove();
        } else {
          dialog.classList.remove('closing');
        }
      }); // Remove or hide dialog node when closing animation has finished.
      document.body.removeAttribute('style'); // Enable again.
    }
  }

}

const findDialog = elem => elem.classList.contains('dialog')
  ? elem
  : elem.closest('.dialog');