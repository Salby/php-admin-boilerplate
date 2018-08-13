class SearchBox {
  constructor(query, config) {
    // Set configurations.
    config = config || {};
    this.placeholder = config.placeholder || 'Search';
    this.emptyState = config.emptyState || "<div class='search-box__empty-state'>No results</div>";

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
    // Find searchbox element.
    el.box = el.querySelector('.search-box');
    // Get name for inputs.
    el.name = el.box.getAttribute('data-name');

    // Parse list data from data attribute.
    el.json = JSON.parse(el.box.getAttribute('data-list'));
    console.table(el.json);
    // Remove attribute.
    el.box.removeAttribute('data-list');

    // Get list container.
    el.listContainer = el.box.querySelector('.search-box__container');
    // Get initial list.
    let initialList = this.list(el.json, {
      name: el.name
    });

    el.update = list => {
      let listItems = list.querySelectorAll('li');
      listItems.forEach(item => {
        let label = item.querySelector('label');
        item.addEventListener('click', () => {
          el.input.innerHTML = label.innerHTML;
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
        list.appendChild(this.listItem(obj, config));
      });
    } else {
      INDEXES.forEach(index => {
        list.appendChild(this.listItem(json[index], config));
      });
    }
    return list;
  }
  listItem(obj, config) {
    config = config || {};
    const NAME = config.name || '';

    const DATA = Object.values(obj); // Parse object as array.
    const ID = guid(); // Generate new id.
    let item = document.createElement('li');
    let label = `${capitalize(DATA[1])}`; // Capitalize first letter in label.
    label = label.replace('_', ' '); // Replace underscores with spaces (it's prettier).
    item.innerHTML = `<input type="radio" name="${NAME}" id="${ID}" value="${DATA[0]}"><label for="${ID}">${label}</label>`;
    return item; // Return list item.
  }

  open(el) {
    /*if (el.classList.contains('closing'))
      el.classList.remove('closing');*/
    el.classList.add('open');
    let input = el.querySelector('input');
    input.focus();
  }

  close(el) {
    el.classList.replace('open', 'closing');
    el.addEventListener('animationend', () => el.classList.remove('closing'));
  }

  updateList(container, list, callback) {
    container.innerHTML = "";
    container.appendChild(list);
    callback(container.childNodes[0]);
  }

}

/*const addMultipleListeners = (el, listeners, fun) => {
  listeners.forEach(listener => {
    el.addEventListener(listener, fun(event));
  });
};*/
const addMultipleListeners = (el, events, handler, useCapture = false, args) => {
  if (!(events instanceof Array)) {
    throw `addMultipleListeners: \n
           Please supply an array of events \n
           (like ['click', 'mouseover'])`;
  }
  let handlerFunction = (e) => {
    handler.apply(this, args && args instanceof Array ? args : []);
  };
  events.forEach(event => {
    el.addEventListener(event, handlerFunction(), useCapture);
  });
};
const removeMultipleListeners = (el, listeners, action = undefined) => {
  listeners.forEach(listener => {
    el.removeEventListener(listener, action);
  })
};

const isOpen = el => el.className.contains('open');

const s4 = () => (1+Math.random()*0x10000|0).toString(16).substring(1);
const guid = () => `${s4()}${s4()}-${s4()}-${s4()}-${s4()}-${s4()}${s4()}${s4()}`;

const capitalize = string => string[0].toUpperCase() + string.slice(1);

new SearchBox('.form__group--searchbox');

