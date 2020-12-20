#
# Table structure for table 'tx_in2studyfinder_domain_model_globaldata'
#
CREATE TABLE tx_in2studyfinder_domain_model_globaldata
(

    uid              int(11)                         NOT NULL auto_increment,
    pid              int(11)             DEFAULT '0' NOT NULL,

    title            varchar(255)        DEFAULT ''  NOT NULL,
    default_preset   int(11) unsigned    DEFAULT '0' NOT NULL,

    tstamp           int(11) unsigned    DEFAULT '0' NOT NULL,
    crdate           int(11) unsigned    DEFAULT '0' NOT NULL,
    cruser_id        int(11) unsigned    DEFAULT '0' NOT NULL,
    deleted          tinyint(4) unsigned DEFAULT '0' NOT NULL,
    hidden           tinyint(4) unsigned DEFAULT '0' NOT NULL,
    starttime        int(11) unsigned    DEFAULT '0' NOT NULL,
    endtime          int(11) unsigned    DEFAULT '0' NOT NULL,

    t3ver_oid        int(11)             DEFAULT '0' NOT NULL,
    t3ver_id         int(11)             DEFAULT '0' NOT NULL,
    t3ver_wsid       int(11)             DEFAULT '0' NOT NULL,
    t3ver_label      varchar(255)        DEFAULT ''  NOT NULL,
    t3ver_state      tinyint(4)          DEFAULT '0' NOT NULL,
    t3ver_stage      int(11)             DEFAULT '0' NOT NULL,
    t3ver_count      int(11)             DEFAULT '0' NOT NULL,
    t3ver_tstamp     int(11)             DEFAULT '0' NOT NULL,
    t3ver_move_id    int(11)             DEFAULT '0' NOT NULL,
    sorting          int(11)             DEFAULT '0' NOT NULL,

    sys_language_uid int(11)             DEFAULT '0' NOT NULL,
    l10n_parent      int(11)             DEFAULT '0' NOT NULL,
    l10n_diffsource  mediumblob,

    PRIMARY KEY (uid),
    KEY parent (pid),
    KEY t3ver_oid (t3ver_oid, t3ver_wsid),
    KEY language (l10n_parent, sys_language_uid)

);

#
# Table structure for table 'tx_in2studyfinder_domain_model_studycourse'
#
CREATE TABLE tx_in2studyfinder_domain_model_studycourse
(

    uid                      int(11)                            NOT NULL auto_increment,
    pid                      int(11)             DEFAULT '0'    NOT NULL,

    title                    varchar(255)        DEFAULT ''     NOT NULL,
    standard_period_of_study int(11)             DEFAULT '0'    NOT NULL,
    ects_credits             int(11)             DEFAULT '0'    NOT NULL,
    tuition_fee              double(11, 2)       DEFAULT '0.00' NOT NULL,
    teaser                   text                               NOT NULL,
    description              text                               NOT NULL,
    university_place         int(11)             DEFAULT '0'    NOT NULL,
    content_elements         int(11) unsigned    DEFAULT '0'    NOT NULL,
    academic_degree          int(11) unsigned    DEFAULT '0',
    department               int(11) unsigned    DEFAULT '0',
    faculty                  int(11) unsigned    DEFAULT '0',
    types_of_study           int(11) unsigned    DEFAULT '0',
    course_languages         int(11) unsigned    DEFAULT '0',
    admission_requirements   int(11) unsigned    DEFAULT '0',
    starts_of_study          int(11) unsigned    DEFAULT '0',
    meta_pagetitle           varchar(100)        DEFAULT ''     NOT NULL,
    meta_keywords            varchar(255)        DEFAULT ''     NOT NULL,
    meta_description         varchar(750)        DEFAULT ''     NOT NULL,

    different_preset         tinyint(4)          DEFAULT '0'    NOT NULL,
    global_data_preset       int(11) unsigned    DEFAULT '0',

    url_segment              varchar(255)        DEFAULT ''     NOT NULL,

    categories               int(11) unsigned    DEFAULT '0'    NOT NULL,

    tstamp                   int(11) unsigned    DEFAULT '0'    NOT NULL,
    crdate                   int(11) unsigned    DEFAULT '0'    NOT NULL,
    cruser_id                int(11) unsigned    DEFAULT '0'    NOT NULL,
    deleted                  tinyint(4) unsigned DEFAULT '0'    NOT NULL,
    hidden                   tinyint(4) unsigned DEFAULT '0'    NOT NULL,
    starttime                int(11) unsigned    DEFAULT '0'    NOT NULL,
    endtime                  int(11) unsigned    DEFAULT '0'    NOT NULL,

    t3ver_oid                int(11)             DEFAULT '0'    NOT NULL,
    t3ver_id                 int(11)             DEFAULT '0'    NOT NULL,
    t3ver_wsid               int(11)             DEFAULT '0'    NOT NULL,
    t3ver_label              varchar(255)        DEFAULT ''     NOT NULL,
    t3ver_state              tinyint(4)          DEFAULT '0'    NOT NULL,
    t3ver_stage              int(11)             DEFAULT '0'    NOT NULL,
    t3ver_count              int(11)             DEFAULT '0'    NOT NULL,
    t3ver_tstamp             int(11)             DEFAULT '0'    NOT NULL,
    t3ver_move_id            int(11)             DEFAULT '0'    NOT NULL,
    sorting                  int(11)             DEFAULT '0'    NOT NULL,

    sys_language_uid         int(11)             DEFAULT '0'    NOT NULL,
    l10n_parent              int(11)             DEFAULT '0'    NOT NULL,
    l10n_diffsource          mediumblob,

    PRIMARY KEY (uid),
    KEY parent (pid),
    KEY t3ver_oid (t3ver_oid, t3ver_wsid),
    KEY language (l10n_parent, sys_language_uid)

);

#
# Table structure for table 'tx_in2studyfinder_domain_model_academicdegree'
#
CREATE TABLE tx_in2studyfinder_domain_model_academicdegree
(

    uid              int(11)                         NOT NULL auto_increment,
    pid              int(11)             DEFAULT '0' NOT NULL,

    degree           varchar(255)        DEFAULT ''  NOT NULL,
    graduation       int(11) unsigned    DEFAULT '0',

    tstamp           int(11) unsigned    DEFAULT '0' NOT NULL,
    crdate           int(11) unsigned    DEFAULT '0' NOT NULL,
    cruser_id        int(11) unsigned    DEFAULT '0' NOT NULL,
    deleted          tinyint(4) unsigned DEFAULT '0' NOT NULL,
    hidden           tinyint(4) unsigned DEFAULT '0' NOT NULL,
    starttime        int(11) unsigned    DEFAULT '0' NOT NULL,
    endtime          int(11) unsigned    DEFAULT '0' NOT NULL,

    t3ver_oid        int(11)             DEFAULT '0' NOT NULL,
    t3ver_id         int(11)             DEFAULT '0' NOT NULL,
    t3ver_wsid       int(11)             DEFAULT '0' NOT NULL,
    t3ver_label      varchar(255)        DEFAULT ''  NOT NULL,
    t3ver_state      tinyint(4)          DEFAULT '0' NOT NULL,
    t3ver_stage      int(11)             DEFAULT '0' NOT NULL,
    t3ver_count      int(11)             DEFAULT '0' NOT NULL,
    t3ver_tstamp     int(11)             DEFAULT '0' NOT NULL,
    t3ver_move_id    int(11)             DEFAULT '0' NOT NULL,

    sys_language_uid int(11)             DEFAULT '0' NOT NULL,
    l10n_parent      int(11)             DEFAULT '0' NOT NULL,
    l10n_diffsource  mediumblob,

    PRIMARY KEY (uid),
    KEY parent (pid),
    KEY t3ver_oid (t3ver_oid, t3ver_wsid),
    KEY language (l10n_parent, sys_language_uid)

);

#
# Table structure for table 'tx_in2studyfinder_domain_model_department'
#
CREATE TABLE tx_in2studyfinder_domain_model_department
(

    uid              int(11)                         NOT NULL auto_increment,
    pid              int(11)             DEFAULT '0' NOT NULL,

    title            varchar(255)        DEFAULT ''  NOT NULL,

    tstamp           int(11) unsigned    DEFAULT '0' NOT NULL,
    crdate           int(11) unsigned    DEFAULT '0' NOT NULL,
    cruser_id        int(11) unsigned    DEFAULT '0' NOT NULL,
    deleted          tinyint(4) unsigned DEFAULT '0' NOT NULL,
    hidden           tinyint(4) unsigned DEFAULT '0' NOT NULL,
    starttime        int(11) unsigned    DEFAULT '0' NOT NULL,
    endtime          int(11) unsigned    DEFAULT '0' NOT NULL,

    t3ver_oid        int(11)             DEFAULT '0' NOT NULL,
    t3ver_id         int(11)             DEFAULT '0' NOT NULL,
    t3ver_wsid       int(11)             DEFAULT '0' NOT NULL,
    t3ver_label      varchar(255)        DEFAULT ''  NOT NULL,
    t3ver_state      tinyint(4)          DEFAULT '0' NOT NULL,
    t3ver_stage      int(11)             DEFAULT '0' NOT NULL,
    t3ver_count      int(11)             DEFAULT '0' NOT NULL,
    t3ver_tstamp     int(11)             DEFAULT '0' NOT NULL,
    t3ver_move_id    int(11)             DEFAULT '0' NOT NULL,

    sys_language_uid int(11)             DEFAULT '0' NOT NULL,
    l10n_parent      int(11)             DEFAULT '0' NOT NULL,
    l10n_diffsource  mediumblob,

    PRIMARY KEY (uid),
    KEY parent (pid),
    KEY t3ver_oid (t3ver_oid, t3ver_wsid),
    KEY language (l10n_parent, sys_language_uid)

);

#
# Table structure for table 'tx_in2studyfinder_domain_model_faculty'
#
CREATE TABLE tx_in2studyfinder_domain_model_faculty
(

    uid              int(11)                         NOT NULL auto_increment,
    pid              int(11)             DEFAULT '0' NOT NULL,

    title            varchar(255)        DEFAULT ''  NOT NULL,

    tstamp           int(11) unsigned    DEFAULT '0' NOT NULL,
    crdate           int(11) unsigned    DEFAULT '0' NOT NULL,
    cruser_id        int(11) unsigned    DEFAULT '0' NOT NULL,
    deleted          tinyint(4) unsigned DEFAULT '0' NOT NULL,
    hidden           tinyint(4) unsigned DEFAULT '0' NOT NULL,
    starttime        int(11) unsigned    DEFAULT '0' NOT NULL,
    endtime          int(11) unsigned    DEFAULT '0' NOT NULL,

    t3ver_oid        int(11)             DEFAULT '0' NOT NULL,
    t3ver_id         int(11)             DEFAULT '0' NOT NULL,
    t3ver_wsid       int(11)             DEFAULT '0' NOT NULL,
    t3ver_label      varchar(255)        DEFAULT ''  NOT NULL,
    t3ver_state      tinyint(4)          DEFAULT '0' NOT NULL,
    t3ver_stage      int(11)             DEFAULT '0' NOT NULL,
    t3ver_count      int(11)             DEFAULT '0' NOT NULL,
    t3ver_tstamp     int(11)             DEFAULT '0' NOT NULL,
    t3ver_move_id    int(11)             DEFAULT '0' NOT NULL,

    sys_language_uid int(11)             DEFAULT '0' NOT NULL,
    l10n_parent      int(11)             DEFAULT '0' NOT NULL,
    l10n_diffsource  mediumblob,

    PRIMARY KEY (uid),
    KEY parent (pid),
    KEY t3ver_oid (t3ver_oid, t3ver_wsid),
    KEY language (l10n_parent, sys_language_uid)

);

#
# Table structure for table 'tx_in2studyfinder_domain_model_typeofstudy'
#
CREATE TABLE tx_in2studyfinder_domain_model_typeofstudy
(

    uid              int(11)                         NOT NULL auto_increment,
    pid              int(11)             DEFAULT '0' NOT NULL,

    type             varchar(255)        DEFAULT ''  NOT NULL,

    tstamp           int(11) unsigned    DEFAULT '0' NOT NULL,
    crdate           int(11) unsigned    DEFAULT '0' NOT NULL,
    cruser_id        int(11) unsigned    DEFAULT '0' NOT NULL,
    deleted          tinyint(4) unsigned DEFAULT '0' NOT NULL,
    hidden           tinyint(4) unsigned DEFAULT '0' NOT NULL,
    starttime        int(11) unsigned    DEFAULT '0' NOT NULL,
    endtime          int(11) unsigned    DEFAULT '0' NOT NULL,

    t3ver_oid        int(11)             DEFAULT '0' NOT NULL,
    t3ver_id         int(11)             DEFAULT '0' NOT NULL,
    t3ver_wsid       int(11)             DEFAULT '0' NOT NULL,
    t3ver_label      varchar(255)        DEFAULT ''  NOT NULL,
    t3ver_state      tinyint(4)          DEFAULT '0' NOT NULL,
    t3ver_stage      int(11)             DEFAULT '0' NOT NULL,
    t3ver_count      int(11)             DEFAULT '0' NOT NULL,
    t3ver_tstamp     int(11)             DEFAULT '0' NOT NULL,
    t3ver_move_id    int(11)             DEFAULT '0' NOT NULL,

    sys_language_uid int(11)             DEFAULT '0' NOT NULL,
    l10n_parent      int(11)             DEFAULT '0' NOT NULL,
    l10n_diffsource  mediumblob,

    PRIMARY KEY (uid),
    KEY parent (pid),
    KEY t3ver_oid (t3ver_oid, t3ver_wsid),
    KEY language (l10n_parent, sys_language_uid)

);

#
# Table structure for table 'tx_in2studyfinder_domain_model_courselanguage'
#
CREATE TABLE tx_in2studyfinder_domain_model_courselanguage
(

    uid              int(11)                         NOT NULL auto_increment,
    pid              int(11)             DEFAULT '0' NOT NULL,

    language         varchar(255)        DEFAULT ''  NOT NULL,

    tstamp           int(11) unsigned    DEFAULT '0' NOT NULL,
    crdate           int(11) unsigned    DEFAULT '0' NOT NULL,
    cruser_id        int(11) unsigned    DEFAULT '0' NOT NULL,
    deleted          tinyint(4) unsigned DEFAULT '0' NOT NULL,
    hidden           tinyint(4) unsigned DEFAULT '0' NOT NULL,
    starttime        int(11) unsigned    DEFAULT '0' NOT NULL,
    endtime          int(11) unsigned    DEFAULT '0' NOT NULL,

    t3ver_oid        int(11)             DEFAULT '0' NOT NULL,
    t3ver_id         int(11)             DEFAULT '0' NOT NULL,
    t3ver_wsid       int(11)             DEFAULT '0' NOT NULL,
    t3ver_label      varchar(255)        DEFAULT ''  NOT NULL,
    t3ver_state      tinyint(4)          DEFAULT '0' NOT NULL,
    t3ver_stage      int(11)             DEFAULT '0' NOT NULL,
    t3ver_count      int(11)             DEFAULT '0' NOT NULL,
    t3ver_tstamp     int(11)             DEFAULT '0' NOT NULL,
    t3ver_move_id    int(11)             DEFAULT '0' NOT NULL,

    sys_language_uid int(11)             DEFAULT '0' NOT NULL,
    l10n_parent      int(11)             DEFAULT '0' NOT NULL,
    l10n_diffsource  mediumblob,

    PRIMARY KEY (uid),
    KEY parent (pid),
    KEY t3ver_oid (t3ver_oid, t3ver_wsid),
    KEY language (l10n_parent, sys_language_uid)

);

#
# Table structure for table 'tx_in2studyfinder_domain_model_admissionrequirement'
#
CREATE TABLE tx_in2studyfinder_domain_model_admissionrequirement
(

    uid              int(11)                         NOT NULL auto_increment,
    pid              int(11)             DEFAULT '0' NOT NULL,

    title            varchar(255)        DEFAULT ''  NOT NULL,

    tstamp           int(11) unsigned    DEFAULT '0' NOT NULL,
    crdate           int(11) unsigned    DEFAULT '0' NOT NULL,
    cruser_id        int(11) unsigned    DEFAULT '0' NOT NULL,
    deleted          tinyint(4) unsigned DEFAULT '0' NOT NULL,
    hidden           tinyint(4) unsigned DEFAULT '0' NOT NULL,
    starttime        int(11) unsigned    DEFAULT '0' NOT NULL,
    endtime          int(11) unsigned    DEFAULT '0' NOT NULL,

    t3ver_oid        int(11)             DEFAULT '0' NOT NULL,
    t3ver_id         int(11)             DEFAULT '0' NOT NULL,
    t3ver_wsid       int(11)             DEFAULT '0' NOT NULL,
    t3ver_label      varchar(255)        DEFAULT ''  NOT NULL,
    t3ver_state      tinyint(4)          DEFAULT '0' NOT NULL,
    t3ver_stage      int(11)             DEFAULT '0' NOT NULL,
    t3ver_count      int(11)             DEFAULT '0' NOT NULL,
    t3ver_tstamp     int(11)             DEFAULT '0' NOT NULL,
    t3ver_move_id    int(11)             DEFAULT '0' NOT NULL,

    sys_language_uid int(11)             DEFAULT '0' NOT NULL,
    l10n_parent      int(11)             DEFAULT '0' NOT NULL,
    l10n_diffsource  mediumblob,

    PRIMARY KEY (uid),
    KEY parent (pid),
    KEY t3ver_oid (t3ver_oid, t3ver_wsid),
    KEY language (l10n_parent, sys_language_uid)

);

#
# Table structure for table 'tx_in2studyfinder_domain_model_startofstudy'
#
CREATE TABLE tx_in2studyfinder_domain_model_startofstudy
(

    uid              int(11)                         NOT NULL auto_increment,
    pid              int(11)             DEFAULT '0' NOT NULL,

    title            varchar(255)        DEFAULT ''  NOT NULL,
    start_date       int(11) unsigned    DEFAULT '0' NOT NULL,

    tstamp           int(11) unsigned    DEFAULT '0' NOT NULL,
    crdate           int(11) unsigned    DEFAULT '0' NOT NULL,
    cruser_id        int(11) unsigned    DEFAULT '0' NOT NULL,
    deleted          tinyint(4) unsigned DEFAULT '0' NOT NULL,
    hidden           tinyint(4) unsigned DEFAULT '0' NOT NULL,
    starttime        int(11) unsigned    DEFAULT '0' NOT NULL,
    endtime          int(11) unsigned    DEFAULT '0' NOT NULL,

    t3ver_oid        int(11)             DEFAULT '0' NOT NULL,
    t3ver_id         int(11)             DEFAULT '0' NOT NULL,
    t3ver_wsid       int(11)             DEFAULT '0' NOT NULL,
    t3ver_label      varchar(255)        DEFAULT ''  NOT NULL,
    t3ver_state      tinyint(4)          DEFAULT '0' NOT NULL,
    t3ver_stage      int(11)             DEFAULT '0' NOT NULL,
    t3ver_count      int(11)             DEFAULT '0' NOT NULL,
    t3ver_tstamp     int(11)             DEFAULT '0' NOT NULL,
    t3ver_move_id    int(11)             DEFAULT '0' NOT NULL,

    sys_language_uid int(11)             DEFAULT '0' NOT NULL,
    l10n_parent      int(11)             DEFAULT '0' NOT NULL,
    l10n_diffsource  mediumblob,

    PRIMARY KEY (uid),
    KEY parent (pid),
    KEY t3ver_oid (t3ver_oid, t3ver_wsid),
    KEY language (l10n_parent, sys_language_uid)

);

#
# Table structure for table 'tx_in2studyfinder_domain_model_graduation'
#
CREATE TABLE tx_in2studyfinder_domain_model_graduation
(

    uid              int(11)                         NOT NULL auto_increment,
    pid              int(11)             DEFAULT '0' NOT NULL,

    title            varchar(255)        DEFAULT ''  NOT NULL,

    tstamp           int(11) unsigned    DEFAULT '0' NOT NULL,
    crdate           int(11) unsigned    DEFAULT '0' NOT NULL,
    cruser_id        int(11) unsigned    DEFAULT '0' NOT NULL,
    deleted          tinyint(4) unsigned DEFAULT '0' NOT NULL,
    hidden           tinyint(4) unsigned DEFAULT '0' NOT NULL,
    starttime        int(11) unsigned    DEFAULT '0' NOT NULL,
    endtime          int(11) unsigned    DEFAULT '0' NOT NULL,

    t3ver_oid        int(11)             DEFAULT '0' NOT NULL,
    t3ver_id         int(11)             DEFAULT '0' NOT NULL,
    t3ver_wsid       int(11)             DEFAULT '0' NOT NULL,
    t3ver_label      varchar(255)        DEFAULT ''  NOT NULL,
    t3ver_state      tinyint(4)          DEFAULT '0' NOT NULL,
    t3ver_stage      int(11)             DEFAULT '0' NOT NULL,
    t3ver_count      int(11)             DEFAULT '0' NOT NULL,
    t3ver_tstamp     int(11)             DEFAULT '0' NOT NULL,
    t3ver_move_id    int(11)             DEFAULT '0' NOT NULL,

    sys_language_uid int(11)             DEFAULT '0' NOT NULL,
    l10n_parent      int(11)             DEFAULT '0' NOT NULL,
    l10n_diffsource  mediumblob,

    PRIMARY KEY (uid),
    KEY parent (pid),
    KEY t3ver_oid (t3ver_oid, t3ver_wsid),
    KEY language (l10n_parent, sys_language_uid)

);

#
# Table structure for table 'tx_in2studyfinder_studycourse_ttcontent_mm'
#
CREATE TABLE tx_in2studyfinder_studycourse_ttcontent_mm
(
    uid_local       int(11) unsigned DEFAULT '0' NOT NULL,
    uid_foreign     int(11) unsigned DEFAULT '0' NOT NULL,
    sorting         int(11) unsigned DEFAULT '0' NOT NULL,
    sorting_foreign int(11) unsigned DEFAULT '0' NOT NULL,

    KEY uid_local (uid_local),
    KEY uid_foreign (uid_foreign)
);

#
# Table structure for table 'tx_in2studyfinder_studycourse_courselanguage_mm'
#
CREATE TABLE tx_in2studyfinder_studycourse_courselanguage_mm
(
    uid_local       int(11) unsigned DEFAULT '0' NOT NULL,
    uid_foreign     int(11) unsigned DEFAULT '0' NOT NULL,
    sorting         int(11) unsigned DEFAULT '0' NOT NULL,
    sorting_foreign int(11) unsigned DEFAULT '0' NOT NULL,

    KEY uid_local (uid_local),
    KEY uid_foreign (uid_foreign)
);

#
# Table structure for table 'tx_in2studyfinder_studycourse_startofstudy_mm'
#
CREATE TABLE tx_in2studyfinder_studycourse_startofstudy_mm
(
    uid_local       int(11) unsigned DEFAULT '0' NOT NULL,
    uid_foreign     int(11) unsigned DEFAULT '0' NOT NULL,
    sorting         int(11) unsigned DEFAULT '0' NOT NULL,
    sorting_foreign int(11) unsigned DEFAULT '0' NOT NULL,

    KEY uid_local (uid_local),
    KEY uid_foreign (uid_foreign)
);

#
# Table structure for table 'tx_in2studyfinder_studycourse_typeofstudy_mm'
#
CREATE TABLE tx_in2studyfinder_studycourse_typeofstudy_mm
(
    uid_local       int(11) unsigned DEFAULT '0' NOT NULL,
    uid_foreign     int(11) unsigned DEFAULT '0' NOT NULL,
    sorting         int(11) unsigned DEFAULT '0' NOT NULL,
    sorting_foreign int(11) unsigned DEFAULT '0' NOT NULL,

    KEY uid_local (uid_local),
    KEY uid_foreign (uid_foreign)
);

#
# Table structure for table 'tx_in2studyfinder_studycourse_admissionrequirement_mm'
#
CREATE TABLE tx_in2studyfinder_studycourse_admissionrequirement_mm
(
    uid_local       int(11) unsigned DEFAULT '0' NOT NULL,
    uid_foreign     int(11) unsigned DEFAULT '0' NOT NULL,
    sorting         int(11) unsigned DEFAULT '0' NOT NULL,
    sorting_foreign int(11) unsigned DEFAULT '0' NOT NULL,

    KEY uid_local (uid_local),
    KEY uid_foreign (uid_foreign)
);
