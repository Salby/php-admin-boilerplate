/*
class SearchBox {
  constructor(query, config) {
    // Set configurations.
    config = config || {};
    this.placeholder = config.placeholder || 'Search';
    this.emptyState = config.emptyState || "<div class='search-box__empty-state'>" +
                                             "<button type='button' class='button__flat--primary' id='add-new'>Add new</button>" +
                                           "</div>";

    // Get results from query.
    this.queryRes = document.querySelectorAll(query);

    if (this.queryRes) {
      // Initialize searchboxes.
      this.queryRes.forEach(searchBox => {
        if (!searchBox.initialized) {
          // Initialize searchbox if it isn't already.
          this.init(searchBox);
          // Mark as initialized.
          searchBox.initialized = true;
        }
      });
    }
  }

  init(el) {

    // Find input element.
    el.input = el.querySelector('.input');
    this.event = new Event('input-value-changed');
    el.input.addEventListener('input-value-changed', () => {
      this.updateInput(el.input);
    });
    // Find searchbox element.
    el.box = el.querySelector('.search-box');
    // Get name for inputs.
    el.name = el.box.getAttribute('data-name');

    // Parse list data from data attribute.
    el.json = JSON.parse(el.box.getAttribute('data-list'));
    console.table(el.json);
    // Remove attribute.
    el.box.removeAttribute('data-list');

    // Find hidden input if it exists.
    el.newItem = el.querySelector('#new-item')
      ? el.querySelector('#new-item')
      : false;

    // Get list container.
    el.listContainer = el.box.querySelector('.search-box__container');
    // Get initial list.
    let initialList = this.list(el.json, {
      name: el.name
    });

    el.update = list => {
      let listItems = list.querySelectorAll('li');
      listItems.forEach(item => {
        item.addEventListener('click', event => {
          event.stopPropagation();
          console.log('click!');
          el.input.innerHTML = item.innerText;
          this.addNew(el, item.innerText);
          this.close(el.box);
        });
      });
    };

    // Insert initial list.
    el.listContainer.appendChild(initialList);
    this.updateList(el.listContainer, initialList, list => el.update(list));

    // Find search input.
    el.search = el.box.querySelector('input');
    // Set placeholder in search input.
    el.search.setAttribute('placeholder', this.placeholder);
    // Listen for change in value and search.
    el.search.addEventListener('keyup', () => {
      const QUERY = el.search.value;
      if (QUERY === '') {
        el.listContainer.appendChild(initialList);
        this.updateList(el.listContainer, initialList, list => el.update(list));
      } else {
        let indexes = this.search(el.json, QUERY);
        if (!indexes.length) {
          el.listContainer.innerHTML = this.emptyState;
          el.addNew = el.listContainer.querySelector('#add-new');
          el.addNew.addEventListener('click', () => this.addNew(el, el.search.value));
          this.close(el.box);
        } else {
          // Update list.
          const RESULT = this.list(el.json, {
            name: el.name,
            indexes: indexes
          });
          // Insert into container.
          el.listContainer.appendChild(RESULT);
          this.updateList(el.listContainer, RESULT, list => el.update(list));
        }
      }
    });


    el.input.addEventListener('click', () => {
      this.open(el.box);
    });
    window.addEventListener('click', event => {
      if (!event.target.closest('.form__group--searchbox')) {
        this.close(el.box);
      }
    });
  }

  search(source, query) {
    const RESULT = SearchJSON.match(source, query);
    return RESULT.indexes;
  }

  list(json, config) {
    config = config || {};
    const INDEXES = config.indexes || [];

    let list = document.createElement('ul');
    if (!INDEXES.length) {
      json.forEach(obj => {
        let item = this.listItem(obj, config);
        list.appendChild(item);
      });
    } else {
      INDEXES.forEach(index => {
        let item = this.listItem(json[index], config);
        list.appendChild(item);
      });
    }
    return list;
  }
  listItem(obj, config) {
    config = config || {};
    const NAME = config.name || '';

    const DATA = Object.values(obj); // Parse object as array.
    let item = document.createElement('li');
    let label = `${capitalize(DATA[1])}`; // Capitalize first letter in label.
    label = label.replace('_', ' '); // Replace underscores with spaces (it's prettier).
    //item.innerHTML = `<input type="radio" name="${NAME}" id="${ID}" value="${DATA[0]}"><label for="${ID}">${label}</label>`;
    item.innerText = label;
    item.setAttribute('data-name', NAME);
    item.setAttribute('data-value', DATA[0]);
    return item; // Return list item.
  }

  open(el) {
    console.log('open');
    el.classList.add('open');
    let input = el.querySelector('input');
    input.focus();
  }

  close(el) {
    console.log('close');
    el.classList.replace('open', 'closing');
    el.addEventListener('animationend', () => el.classList.remove('closing'));
  }

  updateList(container, list, callback) {
    container.innerHTML = "";
    container.appendChild(list);
    callback(container.childNodes[0]);
  }

  addNew(el, value) {
    const newItem = capitalize(value);
    el.input.innerHTML = newItem;
    el.input.dispatchEvent(this.event);

    // Create new hidden input if it doesn't exist.
    if (!el.newItem) {
      el.innerHTML += `<input type="hidden" name="${el.name}" id="new-item" value="">`;
      el.newItem = el.querySelector('#new-item');
    }

    // Insert new item value.
    el.newItem.value = newItem;
  }

  updateInput(input) {
    let label = findSibling(input, '.label');
    let empty = input.innerText.length > 0;
    if (empty)
      label.classList.add('hovering');
    else
      label.classList.remove('hovering');
  }

}

const isOpen = el => el.className.contains('open');

const capitalize = string => string[0].toUpperCase() + string.slice(1);

new SearchBox('.form__group--searchbox');

*/
