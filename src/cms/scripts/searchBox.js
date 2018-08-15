class SearchBox {
  constructor(query, config) {
    // Set config.
    config = config || {};
    this.placeholder = config.placeholder || 'Search';
    this.emptyState = config.emptyState ||  "<div class='search-box__empty'>" +
                                              "<button class='button__flat--primary' id='add-new'>" +
                                                "Add new" +
                                              "</button>" +
                                            "</div>"

    // Get results from query.
    this.nodeList = document.querySelectorAll(query);

    if (this.nodeList) {
      // Iterate and initialize searchboxes.
      this.nodeList.forEach(searchBox => {
        if (!searchBox.initialized) {
          this.init(searchBox);
          // Mark as initialized.
          searchBox.initialized = true;
        }
      });
    }
  }

  init(elem) {
    // Dummy input.
    elem.input = elem.querySelector('.input');
    // Create event and add listener to input.
    this.event = new Event('input-changed');
    elem.input.addEventListener('input-changed', () => this.inputState(elem.input));

    // Actual input.
    elem.hiddenInput = elem.querySelector('#input');

    // Searchbox box.
    elem.box = elem.querySelector('.search-box');
    elem.list = elem.querySelector('.search-box__container');

    // JSON.
    elem.JSON = elem.box.getAttribute('data-list');
    // Remove data-list attribute.
    elem.box.removeAttribute('data-list');

    // Insert list function.
    elem.insertList = (indexes = []) => {
      // Create new list.
      let list = SearchList.list(elem.JSON, indexes);
      // Insert list into container.
      elem.list.appendChild(list);
      // Find list items and initialize.
      let listItems = list.querySelectorAll('li');
      listItems.forEach(li => {
        li.addEventListener('click', event => {
          event.stopPropagation();
          const selected = li.dataset.value;
          const label = li.innerHTML;
          this.updateInput(elem.hiddenInput, selected);
          this.updateDummy(elem.input, label);
          this.close(elem.box);
        });
      });
    };

    // Initial list.
    elem.insertList(elem.list, elem.JSON);

    // Search.
    elem.search = elem.querySelector('.search-box__input');
    // Set placeholder.
    elem.search.placeholder = this.placeholder;

    // Open & close.
    elem.input.addEventListener('click', () => {
      if (!elem.box.classList.contains('open'))
        this.open(elem.box);
    });
    window.addEventListener('click', event => {
      if (!event.target.closest('.form__group--searchbox'))
        this.nodeList.forEach(searchBox => {
          this.close(searchBox.box);
        });
    });
  }

  updateInput(input, value) {
    input.value = value;
  }
  updateDummy(dummy, text) {
    dummy.innerText = text;
    dummy.dispatchEvent(this.event);
    this.inputState(dummy);
  }

  inputState(input) {
    let label = input.querySibling('.label');
    const empty = input.innerHTML.length === 0;
    if (!empty) {
      label.classList.add('hovering');
    } else {
      label.classList.remove('hovering');
    }
  }

  open(box) {
    box.classList.add('open');
  }
  close(box) {
    box.classList.replace('open', 'closing');
    box.addEventListener('animationend', () => {
      box.classList.remove('closing');
    });
  }
}

// Search box list class.
class SearchList {
  static list(json, indexes) {
    // Parse JSON.
    const parsedJSON = JSON.parse(json);
    // Create list element.
    let list = document.createElement('ul');
    if (!indexes.length) {
      // Iterate over parsed JSON if no indexes are given.
      parsedJSON.forEach(obj => {
        // Add item to list.
        list.appendChild(this.item(obj));
      });
    } else {
      // Iterate over indexes in parsed JSON.
      indexes.forEach(index => {
        // Add item to list.
        list.appendChild(SearchList.item(parsedJSON[index]));
      });
    }
    // Return finished list.
    return list;
  }

  static item(obj) {
    // Parse object as array.
    const data = Object.values(obj);
    // Create list item element.
    let item = document.createElement('li');
    // Set item content.
    data[1] = data[1].replace('_', ' ');
    item.innerText = data[1].capitalize();
    // Set item data-value.
    item.setAttribute('data-value', data[0]);
    // Return list item.
    return item;
  }
}

String.prototype.capitalize = function() {
  return this.charAt(0).toUpperCase() + this.slice(1);
};
Object.prototype.querySibling = function(query) {
  let parent = this.parentNode;
  return parent.querySelector(query);
};

new SearchBox('.form__group--searchbox');