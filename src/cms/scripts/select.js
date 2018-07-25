class Select {

  constructor() {
    let selectBoxes = document.querySelectorAll('select'); // Find all select-boxes in document.
    selectBoxes.forEach((sb) => {
      if (!sb.initialized) { // Check if select-box isn't initialized.
        this.initSelectBox(sb); // Initialize select-box.
      }
    });
  }

  initSelectBox(elem) {
    // Build new select-box.
    let selectBox = Select.buildSelectBox(elem);
    let selectList = this.buildSelectList(elem, selectBox);

    selectList.setAttribute('data-collapsed', 'true');
    selectList.style.height = '0px';

    let container = elem.parentNode;
    container.classList.replace('form__group', 'form__group--select');
    container.appendChild(selectBox);
    container.appendChild(selectList);

    selectBox.addEventListener('click', (event) => {
      event.stopPropagation();
      Select.closeAll(selectBox);
      elem.classList.toggle('select-hide');
      selectBox.classList.toggle('active');
      let isCollapsed = selectList.getAttribute('data-collapsed') === 'true';
      if (isCollapsed) {
        expandSection(selectList);
        selectList.setAttribute('data-collapsed', 'false');
      } else {
        collapseSection(selectList);
      }
    });

    window.addEventListener('click', Select.closeAll);

    // Set select-box as initialized.
    elem.initialized = true;
  }

  static buildSelectBox(elem) {
    let select = document.createElement('div');
    select.classList.add('select__selected');
    select.innerHTML = elem.options[elem.selectedIndex].innerHTML;

    return select;
  }
  buildSelectList(elem, selectBox) {
    let selectList = document.createElement('ul');
    selectList.classList.add('select__list');

    for (let i = 0; i < elem.length; i++) {
      let option = document.createElement('li');
      option.innerHTML = elem.options[i].innerHTML;

      option.addEventListener('click', () => {

        for (let i = 0; i < elem.length; i++) {
          if (elem.options[i].innerHTML === option.innerHTML) {
            elem.selectedIndex = i;
            selectBox.innerHTML = option.innerHTML;
            let selected = selectList.querySelectorAll('.same-as-selected');
            selected.forEach(o => {
              o.classList.remove('same-as-selected');
            });
            option.classList.add('same-as-selected');
            collapseSection(selectList);
            break;
          }
        }
        selectBox.click();
      });
      selectList.appendChild(option);
    }
    return selectList;
  }

  static closeAll(elem) {
    let selects = document.querySelectorAll('.select__selected');
    let lists = document.querySelectorAll('.select__list');
    let no = [];
    for (let i = 0; i < selects.length; i++) {
      if (elem === selects[i]) {
        no.push(i);
      } else {
        selects[i].classList.remove('active');
      }
    }
    for (let i = 0; i < lists.length; i++) {
      if (no.indexOf(i)) {
        collapseSection(lists[i]);
      }
    }
  }
}

function collapseSection(elem) {
  let sectionHeight = elem.scrollHeight;
  let elementTransition = elem.style.transition;
  elem.style.transition = '';

  requestAnimationFrame(() => {
    elem.style.height = sectionHeight + 'px';
    elem.style.transition = elementTransition;

    requestAnimationFrame(() => {
      elem.style.height = 0 + 'px';
    });
  });

  elem.setAttribute('data-collapsed', 'true');
}
function expandSection(elem) {
  let sectionHeight = elem.scrollHeight;

  let sectionPadding =
    parseInt(window.getComputedStyle(elem, null).getPropertyValue('--animate-padding-top'))
  +
    parseInt(window.getComputedStyle(elem, null).getPropertyValue('--animate-padding-bottom'));

  elem.style.height = sectionHeight + sectionPadding + 'px';

  elem.addEventListener('transitionend', (event) => {
    elem.removeEventListener('transitionend', event.callee);
  });

  elem.setAttribute('data-collapsed', 'false');
}

new Select();