import SelectCoursesModule from './Export/SelectCoursesModule.js';
import SelectPropertiesModule from './Export/SelectPropertiesModule.js';

class ExportModule {

  initialize() {
    SelectCoursesModule.initialize();
    SelectPropertiesModule.initialize();
    this.#addEventListener();
  }

  #addEventListener() {
    const exportButton = document.querySelector('.js-in2studyfinder-export-courses');
    exportButton?.addEventListener('click', () => this.#exportCourses());
  }

  #exportCourses() {
    this.#selectAllProperties();
  }

  #selectAllProperties() {
    const propertiesList = document.querySelector('.js-in2studyfinder-selected-properties-list');
    if (!propertiesList) return;

    for (const option of propertiesList.options) {
      option.selected = true;
    }
  }
}

export default new ExportModule();
