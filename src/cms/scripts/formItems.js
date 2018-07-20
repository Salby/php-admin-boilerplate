class FormItems {
  constructor() {
    let formItemContainers = document.querySelectorAll('.form_items');
    formItemContainers.forEach((container) => {
      if (!container.initialized) {
        this.init(container);
      }
    });
  }

  init(container) {

    // Save and remove model.
    let model = container.querySelector('.form_items_model'); // Find model in container.
    container.model = model; // This is the model we can build from.
    model.remove(); // Remove model.

    container.form = container.closest('form');

    // Create and insert (+) button.
    let button = this.buildButton(); // Create button.
    container.appendChild(button); // Insert button.
    button.addEventListener('click', () => { // Add 'click' event to button.
      this.insertComponent(container, this.buildComponent(container.model)); // Build and insert new component.
      new Form(container.form.id); // Initialize the form...
      new Select(); // initialize Select again...
    });

    // Build and insert first component.
    this.insertComponent(container, this.buildComponent(container.model));

    // Set container as initialized.
    container.initialized = true;
  }

  buildButton() {
    let button = document.createElement('button'); // Create button.
    button.classList.add('form__group-items-new'); // Add class.
    button.innerHTML = "Add <i class='material-icons'>add</i>"; // Add text and icon to button.
    button.type = 'button'; // Set button type as button to avoid submitting.
    return button;
  }

  buildComponent(model) {
    let clone = model.cloneNode(true);
    /*let component = document.createElement('div');
    component.classList.add('form__group-items-group');
    component.innerHTML = clone.innerHTML;
    return component;*/
    let component = document.createElement('div');
    component.classList.add('form__group-items-group');
    if (clone.childNodes.length > 1) {
      component.innerHTML = clone.innerHTML;
    } else {
      component = clone.childNodes[0];
    }
    return component;
  }

  insertComponent(container, component) {
    let lastChild = container.querySelector('.form__group-items-new');
    container.insertBefore(component, lastChild);
  }
}

new FormItems();