define(['TYPO3/CMS/In2studyfinder/Backend/Utility/UiUtility'], function(UiUtility) {
  'use strict';

  var SelectPropertiesModule = {
    selectedPropertiesList: document.querySelector('.js-in2studyfinder-selected-properties-list'),
    availablePropertiesList: document.querySelector('.js-in2studyfinder-property-list')
  };

  /**
   * initialized all functions
   *
   * @return {void}
   */
  SelectPropertiesModule.initialize = function() {
    SelectPropertiesModule.addEventListener();
  };

  /**
   * initialize event listener
   */
  SelectPropertiesModule.addEventListener = function() {

    // click on an available item
    var propertyList = document.querySelector('.js-in2studyfinder-property-list');
    propertyList.addEventListener('click', SelectPropertiesModule.addPropertyToSelectedProperties);

    // click on the remove item button
    var deleteButton = document.querySelector('.js-in2studyfinder-remove-item');
    deleteButton.addEventListener('click', SelectPropertiesModule.removeSelectedItems);

    // click on move up button
    var moveUpButton = document.querySelector('.js-in2studyfinder-move-item-up');
    moveUpButton.addEventListener('click', SelectPropertiesModule.moveSelectedItemsUp);

    // click on move down button
    var moveDownButton = document.querySelector('.js-in2studyfinder-move-item-down');
    moveDownButton.addEventListener('click', SelectPropertiesModule.moveSelectedItemsDown);

    // click on move to the end button
    var moveToEndButton = document.querySelector('.js-in2studyfinder-move-item-end');
    moveToEndButton.addEventListener('click', SelectPropertiesModule.moveSelectedItemsToEnd);

    // click on move to the beginning button
    var moveToBeginButton = document.querySelector('.js-in2studyfinder-move-item-begin');
    moveToBeginButton.addEventListener('click', SelectPropertiesModule.moveSelectedItemsToBegin);
  };

  /**
   * copies the selected option element to the selected items list
   *
   * @param event
   */
  SelectPropertiesModule.addPropertyToSelectedProperties = function(event) {
    var targetOption = event.target;

    if (targetOption.getAttribute('data-in2studyfinder-property-selectable') === 'true') {
      var copiedOption = targetOption.cloneNode(true);
      UiUtility.hideElement(targetOption);
      UiUtility.removeStyles(copiedOption);

      /*
	   *	add parent element name
	   *	this prevents multiple elements with the same label e.g. Title -> Department Title
	   */
      if (copiedOption.getAttribute('data-in2studyfinder-parent-property-label')) {
        copiedOption.insertAdjacentHTML(
          'afterbegin',
          copiedOption.getAttribute('data-in2studyfinder-parent-property-label')
        );
      }
      SelectPropertiesModule.selectedPropertiesList.add(copiedOption);
    }
  };

  /**
   * remove selected items from selected items list
   */
  SelectPropertiesModule.removeSelectedItems = function() {
    var currentSelection = SelectPropertiesModule.getSelectionFromSelectedPropertiesList();

    if (currentSelection.length) {
      for (var i = 0; i < currentSelection.length; i++) {
        var optionElement = currentSelection[i];

        SelectPropertiesModule.selectedPropertiesList.options[optionElement.index].remove();
        SelectPropertiesModule.showPropertyOnAvailablePropertiesList(optionElement.value);
      }
    }
  };

  /**
   * moves the selected items one position up
   */
  SelectPropertiesModule.moveSelectedItemsUp = function() {
    var selection = SelectPropertiesModule.getSelectionFromSelectedPropertiesList();
    var selectedPropertiesList = SelectPropertiesModule.selectedPropertiesList;

    if (selection.length) {
      for (var i = 0; i < selection.length; i++) {
        if (selection[i].index > 0) {
          var indexBefore = selection[i].index - 1;
          var indexAfter = selection[i].index;
          var elementAfter = selection[i];
          var elementBefore = selectedPropertiesList.options[selection[i].index - 1];

          selectedPropertiesList.removeChild(elementAfter);
          selectedPropertiesList.removeChild(elementBefore);

          selectedPropertiesList.add(elementAfter, indexBefore);
          selectedPropertiesList.add(elementBefore, indexAfter);
        }
      }
    }
  };

  /**
   * moves the selected items one position up
   */
  SelectPropertiesModule.moveSelectedItemsDown = function() {
    var selection = SelectPropertiesModule.getSelectionFromSelectedPropertiesList();
    var selectedPropertiesList = SelectPropertiesModule.selectedPropertiesList;

    if (selection.length) {
      for (var i = selection.length - 1; i >= 0; i--) {
        if (selection[i].index + 1 <= selectedPropertiesList.length - 1) {
          var indexBefore = selection[i].index;
          var indexAfter = selection[i].index + 1;
          var elementBefore = selection[i];
          var elementAfter = selectedPropertiesList.options[selection[i].index + 1];

          selectedPropertiesList.removeChild(elementAfter);
          selectedPropertiesList.removeChild(elementBefore);

          selectedPropertiesList.add(elementAfter, indexBefore);
          selectedPropertiesList.add(elementBefore, indexAfter);
        }
      }
    }
  };

  /**
   * moves the selected items to the end of the list
   */
  SelectPropertiesModule.moveSelectedItemsToEnd = function() {
    var selection = SelectPropertiesModule.getSelectionFromSelectedPropertiesList();
    var selectedPropertiesList = SelectPropertiesModule.selectedPropertiesList;

    if (selection.length) {
      for (var i = selection.length - 1; i >= 0; i--) {
        selectedPropertiesList.removeChild(selection[i]);
        selectedPropertiesList.add(selection[i]);
      }
    }
  };

  /**
   * moves the selected items to the begin of the list
   */
  SelectPropertiesModule.moveSelectedItemsToBegin = function() {
    var selection = SelectPropertiesModule.getSelectionFromSelectedPropertiesList();
    var selectedPropertiesList = SelectPropertiesModule.selectedPropertiesList;

    if (selection.length) {
      for (var i = 0; i < selection.length; i++) {
        selectedPropertiesList.removeChild(selection[i]);
        selectedPropertiesList.add(selection[i], 0);
      }
    }
  };

  /**
   * enables visibility of the given property name in the available properties list
   *
   * @param propertyValue
   */
  SelectPropertiesModule.showPropertyOnAvailablePropertiesList = function(propertyValue) {
    var property = SelectPropertiesModule.availablePropertiesList.querySelector('option[value="' + propertyValue + '"]');
    UiUtility.showElementAsBlock(property);
  };

  /**
   * get the current selected items form the selected properties list
   *
   * @returns {Array}
   */
  SelectPropertiesModule.getSelectionFromSelectedPropertiesList = function() {
    var selection = [];
    var selectedPropertiesList = SelectPropertiesModule.selectedPropertiesList;
    /*
	 * get current selection from selectedPropertiesList
	 * we use this method to get the current selection because
	 * "selectedOptions" do not work on IE
	 */
    for (var i = 0; i < SelectPropertiesModule.selectedPropertiesList.length; i++) {
      if (selectedPropertiesList.options[i].selected) {
        selection.push(selectedPropertiesList.options[i]);
      }
    }

    return selection;
  };

  return SelectPropertiesModule;
});
