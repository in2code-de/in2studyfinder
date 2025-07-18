import AjaxUtility from "./../../Utility/AjaxUtility.js";
import UrlUtility from './../../Utility/UrlUtility.js';

class SelectCoursesModule {

    coursesList = [];
    identifiers = {
        checkAllCheckbox: '.js-in2studyfinder-check-all',
        paginationContainer: '.js-in2studyfinder-pagebrowser',
        itemsPerPageSelect: '.js-in2studyfinder-itemsPerPage',
        changeLanguageSelect: '.js-in2studyfinder-recordLanguage',
        courseListTableBody: '.js-in2studyfinder-course-list',
        loader: '.in2js-in2studyfinder-loader',
        loaderActive: '.in2studyfinder-loader--active',
        selectCourseContainer: '.js-in2studyfinder-select-course-container',
        selectedCoursesCount: '.js-in2studyfinder-selected-courses-count',
        paginationTarget: '.js-in2studyfinder-pagination',
        courseCheckbox: '.js-in2studyfinder-select-course',
    };
    elements = {};

    initialize() {
        this.#cacheElements();
        this.#preparePagination();
        this.#prepareSelectedCourses();
        this.#addEventListeners();
        this.#updateSelectedCoursesCount();
    }

    #cacheElements() {
        for (const key in this.identifiers) {
            this.elements[key] = document.querySelector(this.identifiers[key]);
        }
    }

    #addEventListeners() {
        this.elements.checkAllCheckbox?.addEventListener('click', (e) => this.#toggleAllCoursesSelect(e));
        this.elements.paginationContainer?.addEventListener('click', (e) => this.#callPagination(e));
        this.elements.itemsPerPageSelect?.addEventListener('change', (e) => this.#updateItemsPerPage(e));
        this.elements.changeLanguageSelect?.addEventListener('change', (e) => this.#updateRecordLanguage(e));
        this.elements.courseListTableBody?.addEventListener('click', (e) => this.#toggleCourseSelection(e));
    }

    #callPagination(event) {
        event.preventDefault();
        const url = event.target.href;
        if (!url) return;
        const itemsPerPage = this.elements.itemsPerPageSelect.value;

        const fullUrl = UrlUtility.addAttributeToUrl(
            url,
            'itemsPerPage',
            itemsPerPage
        );

        this.#paginationAjaxCall(fullUrl);
    }

    #paginationAjaxCall(url) {
        AjaxUtility.ajaxCall(url, () => this.#onPaginationCallStart(), (response) => this.#onPaginationCallSuccess(response));
    }

    #onPaginationCallStart() {
        this.elements.loader?.classList.add(this.identifiers.loaderActive.substring(1));
    }

    async #onPaginationCallSuccess(response) {
        const responseText = await response.text();
        const parser = new DOMParser();
        const doc = parser.parseFromString(responseText, 'text/html');

        const newContent = doc.querySelector(this.identifiers.selectCourseContainer);

        if (newContent) {
            this.elements.selectCourseContainer.innerHTML = newContent.innerHTML;
        }

        this.initialize(); // Re-initialize to attach listeners to new content
        this.elements.loader?.classList.remove(this.identifiers.loaderActive.substring(1));
    }

    #updateItemsPerPage(event) {
        const url = event.target.selectedOptions[0]?.dataset.action;
        if (url) {
            this.#paginationAjaxCall(url);
        }
    }

    #updateRecordLanguage(event) {
        const url = event.target.selectedOptions[0]?.dataset.action;
        console.log(url);
        if (url && this.#resetCourseList()) {
            this.#paginationAjaxCall(url);
        }
    }

    #toggleCourseSelection(event) {
        const checkbox = event.target;
        if (!checkbox.matches(this.identifiers.courseCheckbox)) return;

        if (checkbox.checked) {
            this.#addCourseToList(checkbox.value);
        } else {
            this.#removeCourseFromList(checkbox.value);
        }
        this.#updateSelectedCoursesCount();
    }

    #toggleAllCoursesSelect(event) {
        const isChecked = event.target.checked;
        const checkboxes = document.querySelectorAll(this.identifiers.courseCheckbox);

        for (const checkbox of checkboxes) {
            checkbox.checked = isChecked;
            const courseUid = checkbox.dataset.in2studyfinderCourseUid;
            if (courseUid) {
                if (isChecked) {
                    this.#addCourseToList(courseUid);
                } else {
                    this.#removeCourseFromList(courseUid);
                }
            }
        }
        this.#updateSelectedCoursesCount();
    }

    #prepareSelectedCourses() {
        if (this.coursesList.length === 0) return;

        for (const courseId of this.coursesList) {
            const checkbox = document.querySelector(`#course-${courseId}`);
            if (checkbox) {
                checkbox.checked = true;
            }
        }
    }

    #addCourseToList(courseUid) {
        if (!this.coursesList.includes(courseUid)) {
            this.coursesList.push(courseUid);
        }
    }

    #removeCourseFromList(courseUid) {
        this.coursesList = this.coursesList.filter(id => id !== courseUid);
    }

    #resetCourseList() {
        if (this.coursesList.length > 0) {
            if (confirm('Alle aktuell gewählten Kurse werden abgewählt. Wollen Sie fortfahren?')) {
                this.coursesList = [];
                this.#updateSelectedCoursesCount();
            } else {
                return false;
            }
        }
        return true;
    }

    #updateSelectedCoursesCount() {
        if (this.elements.selectedCoursesCount) {
            this.elements.selectedCoursesCount.innerHTML = this.coursesList.length;
        }
    }

    #preparePagination() {
        if (this.elements.paginationContainer && this.elements.paginationTarget) {
            this.elements.paginationTarget.appendChild(this.elements.paginationContainer);
        }
    }
}

export default new SelectCoursesModule();
