class Table {
  constructor(tableId, config) {
    config = config || {};
    this.checkboxesQuery = config.checkboxesQuery || '.table__checkbox';
    this.checkboxesMaster = config.checkboxesMaster || 'master';
    this.messageDefault = config.contextualDefault || 'items selected';
    this.messageOne = config.contextualOne || 'item selected';

    this.masterQuery = '.' + this.checkboxesMaster;

    this.table = document.getElementById(tableId);
    this.source = config.source || false;
    this.init(() => {
      this.checkboxes = this.table.querySelectorAll(this.checkboxesQuery);
      this.checkboxes.forEach(checkbox => {
        if (checkbox.classList.contains(this.checkboxesMaster)) {
          this.initCheckbox(checkbox, { master: true });
        } else {
          this.initCheckbox(checkbox);
        }
      });
    });

    this.contextual = new Contextual(this.table.dataset.contextual, {
      messageDefault: this.messageDefault,
      messageOne: this.messageOne
    });

  }

  init(callback) {
    if (this.source) {
      this.limit = this.source.limit;

      xhr.request({
        url: this.source.url,
        method: 'POST',
        data: {
          limit: this.limit,
          offset: this.source.offset
            ? this.source.offset
            : 0
        },
        success: res => {
          this.table.innerHTML = res;
          new Menu('*[class^=menu]');
          callback();
        }
      });

      const ID = this.table.id;
      this.next = document.getElementById(`${ID}-next`);
      this.prev = document.getElementById(`${ID}-prev`);
      this.status = document.getElementById(`${ID}-status`);

      this.max = this.source.max;
      this.updateStatus(1);
      this.updateNext();
      this.updatePrev();

      this.next.addEventListener('click', () => this.updateSource('next'));
      this.prev.addEventListener('click', () => this.updateSource('prev'));

    } else {
      callback();
    }
  }

  updateNext() {
    if (this.step === Math.ceil(this.max/this.limit)) {
      this.next.classList.add('disabled');
    } else {
      this.next.classList.remove('disabled');
    }
  }
  updatePrev() {
    if (this.step === 1) {
      this.prev.classList.add('disabled');
    } else {
      this.prev.classList.remove('disabled');
    }
  }

  updateSource(method) {

    let step;
    if (method === 'next') {
      this.offset = this.limit * this.step;
      step = this.step + 1;
    } else if (method === 'prev') {
      this.offset = this.offset - this.limit;
      step = this.step - 1;
    }

    xhr.request({
      url: this.source.url,
      method: 'POST',
      data: {
        limit: this.limit,
        offset: this.offset
      },
      success: res => {
        this.table.innerHTML = res;
        this.updateStatus(step);
        this.updateNext();
        this.updatePrev();
        this.update();
        new Menu('*[class^=menu]');
      }
    });
  }


  updateStatus(step) {
    this.step = step;
    const max = Math.ceil(this.max/this.limit);
    this.status.innerText = `${this.step} / ${max}`;
  }

  update() {
    let checked = this.table.checked = [];
    let checkboxes = this.checkboxes.length - 1;
    let master = this.table.querySelector(this.masterQuery);

    this.checkboxes.forEach(checkbox => {
      if (!checkbox.master && checkbox.checked) {
        checked.push(checkbox.value);
      }
    });

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
            e.preventDefault();
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
    xhr.request({
      method: 'POST',
      url: url,
      data: { selected: table.checked },
      success: res => new Toast({
        message: res,
        position: 'right',
        timeout: 100000
      })
    });
    let ctable = new Table(tableId);
    ctable.removeRows(table.checked);
    Select.closeAll();
    ctable.update();
    ctable.contextual.update(0);
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
    } else if (count === 0) {
      this.hide();
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