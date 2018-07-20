class Table {
  constructor(tableId, config) {
    config = config || {};
    this.checkboxesQuery = config.checkboxesQuery || '.table__checkbox';
    this.checkboxesMaster = config.checkboxesMaster || 'master';
    this.messageDefault = config.contextualDefault || 'items selected';
    this.messageOne = config.contextualOne || 'item selected';

    this.masterQuery = '.' + this.checkboxesMaster;

    this.table = document.getElementById(tableId);
    this.checkboxes = this.table.querySelectorAll(this.checkboxesQuery);
    
    this.contextual = new Contextual(this.table.dataset.contextual, {
      messageDefault: this.messageDefault,
      messageOne: this.messageOne
    });

    for (let i = 0; i < this.checkboxes.length; i++) {
      let elem = this.checkboxes[i];
      if (elem.classList.contains(this.checkboxesMaster)) {
        this.initCheckbox(elem, {master: true});
      } else {
        this.initCheckbox(elem);
      }
    }

  }

  update() {
    let checked = this.table.checked = [];
    let checkboxes = this.checkboxes.length - 1;
    let master = this.table.querySelector(this.masterQuery);

    for (let i = 0; i < this.checkboxes.length; i++) {
      const checkbox = this.checkboxes[i];
      if (!checkbox.master && checkbox.checked) {
        checked.push(checkbox.value);
      }
    }

    master.checked = checkboxes === checked.length;

    if (checked.length > 0) {
      this.contextual.update(checked.length);
      this.contextual.show();
    } else {
      this.contextual.hide();
    }
  }

  toggleRow(row) {
    row.classList.toggle('selected');
  }
  removeRows(arr) {
    for (let i = 0; i < this.checkboxes.length; i++) {
      const checkbox = this.checkboxes[i];
      for (let j = 0; j < arr.length; j++) {
        if (checkbox.value === arr[j]) checkbox.row.remove();
      }
    }
  }

  initCheckbox(elem, config) {
    config = config || {};
    elem.master = config.master || false;

    elem.row = elem.closest('tr');

    elem.addEventListener('click', (e) => {
      if (elem.master) {
        if (this.checkboxes.length > 1) {
          const newState = elem.checked;
          for (let j = 0; j < this.checkboxes.length; j++) {
            const checkbox = this.checkboxes[j];
            if (!checkbox.master && checkbox.checked !== newState) {
              checkbox.checked = newState;
              this.toggleRow(checkbox.row);
            }
          }
        } else {
          if (this.checkboxes[0].master) {
            e.preventDefault;
          }
        }
      } else { this.toggleRow(elem.row) }

      this.update();
    });
  }

}

let TableActions = {
  delete: (url, tableId) => {
    const table = document.getElementById(tableId);
    xhr.post(url, table.checked, (res) => {
      new Toast({
        'message': res,
        'position': 'right',
        'timeout': 3500
      });
    });
    let ctable = new Table(tableId);
    ctable.removeRows(table.checked);
    Select.closeAll();
    ctable.update();
  },
};


class Contextual {
  constructor(contextualId, config) {
    config = config || {};
    this.messageDefault = config.messageDefault || 'items selected';
    this.messageOne = config.messageOne || 'item selected';

    this.contextual = document.getElementById(contextualId);
  }

  update(count) {
    const toUpdate = this.contextual.querySelector('.contextualAmount');
    let newString = '0 ' + this.messageDefault;
    if (count === 1) {
      newString = count + ' ' + this.messageOne;
    } else if (count > 1) {
      newString = count + ' ' + this.messageDefault;
    }
    toUpdate.innerHTML = newString;
  }

  show() {
    if (!this.contextual.classList.contains('open')) {
      this.contextual.classList.add('open');
    }
  }
  hide() {
    if (this.contextual.classList.contains('open')) {
      this.contextual.classList.remove('open');
      this.contextual.classList.add('closing');
      this.contextual.addEventListener('animationend', () => this.contextual.classList.remove('closing'));
    }
  }

}