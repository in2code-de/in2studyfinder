<?php
namespace In2code\In2studyfinder\Service;

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class GenerateDummyRecordService
{

    /**
     * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var array
     */
    protected $defaultTypes = [
        'StudyCourse',
        'AcademicDegree',
        'AdmissionRequirement',
        'CourseLanguage',
        'Department',
        'Faculty',
        'Graduation',
        'StartOfStudy',
        'TypeOfStudy',
    ];

    protected $extendedTypes = [];

    /**
     * @var array
     */
    protected $typeRepositories = [];

    /**
     * @var array
     */
    protected $typeModels = [];

    /**
     * @var bool
     */
    protected $isExtendExtensionLoaded = false;

    /**
     * @var string
     */
    protected $absoluteExtendExtensionPath = '';

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->objectManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
        if (ExtensionManagementUtility::isLoaded('in2studyfinder_extend')) {
            $this->isExtendExtensionLoaded = true;
            $this->absoluteExtendExtensionPath = ExtensionManagementUtility::extPath('in2studyfinder_extend');
            $this->setExtendedTypes();
        }

        $this->setTypeRepositoriesAndTypeModels();
    }

    public function generateDummySettings($numberOfRecords = 0)
    {
        for ($i = 0; $i <= $numberOfRecords; $i++) {
            foreach ($this->typeModels as $key => $typeModel) {
                if ($key !== 'StudyCourse') {
                    $propertyArray = $typeModel->_getProperties();

                    /** @var \TYPO3\CMS\Extbase\DomainObject\AbstractEntity $modelObject */
                    $modelObject = clone $typeModel;

                    foreach ($propertyArray as $property => $value) {
                        $dataType = gettype($value);

                        if ($property === 'pid') {
                            $data = 6;
                        } elseif ($property === 'sys_language_uid') {
                            $data = -1;
                        } elseif ($property !== 'uid') {

                            $data = $this->getRandomDataByType($dataType);

                        } elseif ($property instanceof ObjectStorage) {
                            /** @var ObjectStorage $property */
                            $data = $this->getRandomDataByType($dataType, $property->current());
                        }
                        $modelObject->_setProperty($property, $data);
                    }
                    \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($this->typeRepositories, __CLASS__ . ' in der Zeile ' . __LINE__);
                    die();
                    //$this->typeRepositories[$key]->add($modelObject);
                }
            }
        }
    }

    public function generateDummyStudycourses($numberOfRecords = 10)
    {

    }

    protected function setExtendedTypes()
    {
        $extendModelPath = $this->absoluteExtendExtensionPath . 'Classes/Domain/Model/';

        if ($extendModelPath) {
            if ($handle = opendir($extendModelPath)) {
                while (($file = readdir($handle)) !== false) {
                    $fileExtension = substr($file, -3);

                    if ($fileExtension === 'php') {
                        $fileName = substr($file, 0, -4);

                        $this->extendedTypes[] = $fileName;
                    }
                }
                closedir($handle);
            }
        }
    }

    protected function setTypeRepositoriesAndTypeModels()
    {

        if ($this->isExtendExtensionLoaded) {
            $this->setExtendedModels();
        }

        foreach ($this->defaultTypes as $defaultType) {
            $this->typeRepositories[$defaultType] = $this->objectManager->get(
                'In2code\\In2studyfinder\\Domain\\Repository\\' . $defaultType . 'Repository'
            );
            $this->typeModels[$defaultType] = $this->objectManager->get(
                'In2code\\In2studyfinder\\Domain\\Model\\' . $defaultType
            );
        }
    }

    protected function setExtendedModels()
    {
        foreach ($this->defaultTypes as $key => $defaultType) {
            if (in_array($defaultType, $this->extendedTypes)) {
                unset($this->defaultTypes[$key]);
            }
        }

        foreach ($this->extendedTypes as $extendedType) {
            $this->typeRepositories[$extendedType] = $this->objectManager->get(
                'In2code\\In2studyfinderExtend\\Domain\\Repository\\' . $extendedType . 'Repository'
            );
            $this->typeModels[$extendedType] = $this->objectManager->get(
                'In2code\\In2studyfinderExtend\\Domain\\Model\\' . $extendedType
            );
        }
    }

    /**
     * @param string $type
     * @param string $objectType
     * @return mixed
     *
     */
    protected function getRandomDataByType(
        $type,
        $objectType = ''
    ) {
//        \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($objectType, __CLASS__ . ' in der Zeile ' . __LINE__);
//        \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($type, __CLASS__ . ' in der Zeile ' . __LINE__);

        $value = null;
        switch ($type) {
            case 'boolean':
                $value = $this->getRandomBoolean();
                break;
            case 'integer':
                $value = $this->getRandomInteger();
                break;
            case 'double':
                $value = $this->getRandomDouble();
                break;
            case 'string':
                $value = $this->getrandomString();
                break;
            case 'array':
                //$value = $this->getDummyArray();
                break;
            case 'object':
                if (array_key_exists(ucfirst($objectType), $this->typeModels)) {
                    /** @var \TYPO3\CMS\Extbase\DomainObject\AbstractEntity $object */
                    $object = $this->typeModels[ucfirst($objectType)];
                    $propertyArray = $object->_getProperties();

                    foreach ($propertyArray as $property => $value) {
                        if ($property !== 'uid' || $property !== 'pid') {
                            $value = $this->getRandomDataByType($property, gettype($value));

                            /** @var \TYPO3\CMS\Extbase\DomainObject\AbstractEntity $newObject */
                            $object->_setProperty($property, $value);
                        }
                        if ($property === 'pid') {
                            $object->setPid(6);
                        }
                    }
                    $value = $object;
                } else {

                }

                break;
            case 'NULL':
                break;
            default:
                break;
        }

        return $value;
    }

    /**
     * @return string
     */
    protected function getRandomString()
    {
        return 'Lorem ipsum dolor sit amet';
    }

    /**
     * @return bool
     */
    protected function getRandomBoolean()
    {
        return rand(0, 1);
    }

    /**
     * @param int $min
     * @param int $max
     * @return int
     */
    protected function getRandomInteger(
        $min = 10,
        $max = 400
    ) {
        return rand($min, $max);
    }

    /**
     * @param int $min
     * @param int $max
     * @param int $decimals
     * @return float
     */
    protected function getRandomDouble(
        $min = 100,
        $max = 500,
        $decimals = 2
    ) {
        $scale = pow(10, $decimals);
        return mt_rand($min * $scale, $max * $scale) / $scale;
    }
}
