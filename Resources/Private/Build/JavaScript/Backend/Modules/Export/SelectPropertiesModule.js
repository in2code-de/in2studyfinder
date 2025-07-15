import UiUtility from './../../Utility/UiUtility.js';

class SelectPropertiesModule {
  elements = {};

  initialize() {
    this.#cacheElements();
    this.#addEventListeners();
  }

  #cacheElements() {
    this.elements = {
      selectedList: document.querySelector('.js-in2studyfinder-selected-properties-list'),
      availableList: document.querySelector('.js-in2studyfinder-property-list'),
      removeButton: document.querySelector('.js-in2studyfinder-remove-item'),
      moveUpButton: document.querySelector('.js-in2studyfinder-move-item-up'),
      moveDownButton: document.querySelector('.js-in2studyfinder-move-item-down'),
      moveEndButton: document.querySelector('.js-in2studyfinder-move-item-end'),
      moveBeginButton: document.querySelector('.js-in2studyfinder-move-item-begin'),
    };
  }

  #addEventListeners() {
    this.elements.availableList?.addEventListener('click', (e) => this.#addPropertyToSelected(e));
    this.elements.removeButton?.addEventListener('click', () => this.#removeSelectedItems());
    this.elements.moveUpButton?.addEventListener('click', () => this.#moveSelectedItemsUp());
    this.elements.moveDownButton?.addEventListener('click', () => this.#moveSelectedItemsDown());
    this.elements.moveEndButton?.addEventListener('click', () => this.#moveSelectedItemsToEnd());
    this.elements.moveBeginButton?.addEventListener('click', () => this.#moveSelectedItemsToBegin());
  }

  #addPropertyToSelected(event) {
    const targetOption = event.target;

    if (targetOption.tagName === 'OPTION' && targetOption.dataset.in2studyfinderPropertySelectable === 'true') {
      const copiedOption = targetOption.cloneNode(true);
      UiUtility.hideElement(targetOption);
      copiedOption.removeAttribute('style');

      const parentLabel = copiedOption.dataset.in2studyfinderParentPropertyLabel;
      if (parentLabel) {
        copiedOption.prepend(`${parentLabel} `);
      }
      this.elements.selectedList?.add(copiedOption);
    }
  }

  #removeSelectedItems() {
    for (const option of this.#getSelection()) {
      this.#showPropertyInAvailableList(option.value);
      option.remove();
    }
  }

  #moveSelectedItemsUp() {
    for (const option of this.#getSelection()) {
      if (option.previousElementSibling) {
        option.previousElementSibling.before(option);
      }
    }
  }

  #moveSelectedItemsDown() {
    // We reverse the array to move items down correctly in a multiple selection
    const selection = this.#getSelection().reverse();
    for (const option of selection) {
      if (option.nextElementSibling) {
        option.nextElementSibling.after(option);
      }
    }
  }

  #moveSelectedItemsToEnd() {
    const list = this.elements.selectedList;
    if (list) {
      for (const option of this.#getSelection()) {
        list.append(option);
      }
    }
  }

  #moveSelectedItemsToBegin() {
    const list = this.elements.selectedList;
    if (list) {
      for (const option of this.#getSelection()) {
        list.prepend(option);
      }
    }
  }

  #showPropertyInAvailableList(propertyValue) {
    const property = this.elements.availableList?.querySelector(`option[value="${propertyValue}"]`);
    if (property) {
      UiUtility.showElementAsBlock(property);
    }
  }

  /**
   * Gets the currently selected options from the "selected" list.
   * @returns {HTMLOptionElement[]} An array of the selected option elements.
   */
  #getSelection() {
    return this.elements.selectedList ? Array.from(this.elements.selectedList.selectedOptions) : [];
  }
}

export default new SelectPropertiesModule();
