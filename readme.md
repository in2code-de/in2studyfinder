# TYPO3 extension in2studyfinder


## Introduction

in2studyfinder is a free and generic TYPO3 extension, which makes it possible to add, maintain and
display courses of studies in a structured way with different filterable views.
This extension is the basic version, which can be extended by various fields, functions and interfaces and is already
widely used by different universities.


## Installation

Require in2studyfinder via copmoser: `composer require in2code/in2studyfinder` or download a current version from [https://github.com/in2code-de/in2studyfinder](in2studyfinder on github) or install in2studyfinder from TER or in the Extension Manager.
Include in2studyfinder's TypoScript Template "Basic Template" and "CSS Template" if you want to have a minimum of styles in the frontend (e.g. if you don't want to style it yourself).
Create a storage folder in your page tree where you will add your study courses and set all `settingsPids` and `storagePids` of in2studyfinder to the storage folder's UID.


### Target group

TYPO3 Websites from

* Colleges + Hochschulen
* Universities + Universitäten


### Examples

#### Screenshots

Integration at the home page of TH OWL:

![Example dashboard overview](Documentation/Images/screenshot_owl_start.png)

Listview at TH OWL:

![Example dashboard overview](Documentation/Images/screenshot_owl_list.png)

Detailview at TH OWL:

![Example dashboard overview](Documentation/Images/screenshot_owl_detail.png)

Listview at TUM:

![Example dashboard overview](Documentation/Images/screenshot_tum_list.png)

Listview at Uni Ulm:

![Example dashboard overview](Documentation/Images/screenshot_uniulm_list.png)


#### Links

* Live examples:
    * https://www.tum.de/studium/studienangebot/
    * https://www.uni-ulm.de/studium/studieren-an-der-uni-ulm/studiengaenge/
    * https://www.th-owl.de/studium/angebote/studiengaenge/
* See full description (german only) under: https://www.in2code.de/produkte/studiengangsfinder/
* Interest in an extension or interface connection? Contact us: <a href="mailto:sandra.pohl@in2code.de">sandra.pohl@in2code.de</a>


## Individual modules and functions

in2studyfinder can be extended by individual importers (e.g. from SLCM, Hochschulkompass, HIS, etc.).
It's also possible to extend it with new fields or additional tables or add new functions like a keyword filter.
Please ask Sandra for more information about additional modules or if you need professional services:

https://www.in2code.de/produkte/studiengangsfinder/

sandra.pohl@in2code.de


## Requirements

Version 6.x:
  * TYPO3 8.7 or 9.5
  * PHP 5.6 or newer

Version 7.x or newer
  * TYPO3 9.5 or newer
  * PHP 7.2 or newer

## Signals

manipulatePropertyBeforeExport: this signal allows to manipulate values before they are exported to the CSV


## FAQ

* Q1: Can I use fe_users or tt_address for the persons?
* A1: Of course, you can map persons to any existing table via TypoScript
* Q2: I need to import persons from an external service, but how?
* A2: Please ask in2code for professional service or individual importers


## Changelog

| Version    | Date       | State      | Description                                                                  |
| ---------- | ---------- | ---------- | ---------------------------------------------------------------------------- |
| 7.2.0      | 2021-10-05 | FEATURE    | if a course is edited the frontend cache of this record is cleared |
| 7.1.1      | 2021-08-31 | BUGFIX     | various bugfixes |
| 7.1.0      | 2020-03-31 | FEATURE    | add single select filters (radio buttons). various bugfixes |
| 7.0.0      | 2020-12-20 | [!!!]FEATURE | add TYPO3 10.4 support, drop TYPO3 8 support, rewrite js to native js (jQuery is currently still needed because of select2) |
| 6.2.10     | 2021-01-08 | BUGFIX     | remove the default value '*' for the tca link wizards because this disallowed all file extensions|
| 6.2.9      | 2021-01-07 | BUGFIX     | allow the overwrite of the target action in the select view helper |
| 6.2.8      | 2020-05-13 | BUGFIX     | fix serialization exception by filter initialization for TYPO3 9.5.17 and above if the caching is enabled |
| 6.2.7      | 2020-01-14 | BUGFIX     | fix "Undeclared arguments.." at TYPO3 9.5.x in the studyfinder backend module |
| 6.2.6      | 2019-12-13 | BUGFIX     | remove debug from studycourse controller |
| 6.2.5      | 2019-10-16 | BUGFIX     | correct wrong prioritization of selected filter restrictions |
| 6.2.4      | 2019-10-04 | TASK       | use cache for fast search view to increase the performance |
| 6.2.3      | 2019-09-24 | BUGFIX     | set no default l10n_display for single select TCA at the TcaUtility. This prevents save issues for required select fields on translated records at TYPO3 7 and 8 (https://forge.typo3.org/issues/77257, https://forge.typo3.org/issues/88452) |
| 6.2.2      | 2019-09-06 | TASK       | make content object data available in the fast search |
| 6.2.1      | 2019-08-27 | BUGFIX     | set settings PID for FacultyRepository, fix typo |
| 6.2.0      | 2019-08-27 | FEATURE    | add new view fast search which shows only an input (select) with the available courses |
| 6.1.0      | 2019-06-03 | FEATURE    | remove unnecessary url attributes of the quick select, add the "manipulatePropertyBeforeExport" signal |
| 6.0.2      | 2019-04-02 | BUGFIX     | add missing query execution, fix html escape from content element renderer, move ext_icon into Public/Icons/ |
| 6.0.1      | 2019-03-18 | BUGFIX     | fix possible detail view exception because the lib.activeDateFormat do not exist |
| 6.0.0      | 2019-03-13 | [!!!]TASK  | add TYPO3 9.5 support |
| 5.0.0      | 2019-03-01 | FEATURE    | adds an backend module with course export. Last release for TYPO3 7.6 |
| 4.0.2      | 2018-11-26 | BUGFIX     | fix 404 redirect on any course detail page. Last release for TYPO3 6.2 |
| 4.0.1      | 2018-10-16 | TASK       | update npm modules and gulp to prevent JavaScript vulnerabilities of outdated node modules |
| 4.0.0      | 2018-08-23 | [!!!]TASK  | use interfaces for the domain objects wherever possible |
| 3.0.8      | 2018-06-06 | BUGFIX     | reduce the page type num to prevent exceeded integer number in 32 bit systems |
| 3.0.7      | 2017-12-07 | BUGFIX     | fix filter behavior if a filter is disabled via typoscript and also set in the backend, add the typoscript option disabledInFrontend for filters |
| 3.0.6      | 2017-11-27 | BUGFIX     | fix wrong ajax URL handling, fix duplicate cache entry if no contentElementStoragePid is set |
| 3.0.5      | 2017-11-27 | BUGFIX     | fix wrong ajax URL if realUrl or a similar extension is used |
| 3.0.4      | 2017-11-23 | BUGFIX     | set the correct storage pids for ajax requests if an "Record Storage Page" is selected |
| 3.0.3      | 2017-10-19 | BUGFIX     | fix broken translations on filter ajax requests. Add a default configuration language configuration 0 = de and 1 = en |
| 3.0.2      | 2017-10-19 | BUGFIX     | Fix leading slashes for typo3 8 if in2studyfinder_extend is used |
| 3.0.1      | 2017-09-22 | BUGFIX     | Fix no filter parameter in the URL, remove use statements in TCA to prevent use conflicts, fix broken filter accordion icons, add button highlighting |
| 3.0.0      | 2017-09-19 | [!!!]TASK  | [!!!] update HTML markup, compatibility for Typo3 8.7 add basic CSS styling, change CSS classes, update javascript handling. For more details look at the release commit 3.0.0 |
| 2.1.2      | 2017-08-30 | BUGFIX     | Use the correct repository if the extension is extended                      |
| 2.1.1      | 2017-08-29 | BUGFIX     | Use correct cmpObj function if the course model has been overwritten         |
| 2.1.0      | 2017-08-24 | [!!!]FEATURE | [!!!] Drop StuyCourseListContextRepository, add option to save selected filter values, For more details look at the release commit 2.1.0 |
| 2.0.4      | 2017-06-28 | BUGFIX     | Always set filter, hide invalid start of study date, use cache API correctly |
| 2.0.3      | 2017-06-13 | BUGFIX     | Some small CSS fixes                                                         |
| 2.0.2      | 2017-06-12 | BUGFIX     | Fix number of requests on quickjump, change loading image with SVG           |
| 2.0.1      | 2017-06-02 | BUGFIX     | Add a readme and license file                                                |
| 2.0.0      | 2017-05-31 | Task       | Initial free release on Github                                               |
