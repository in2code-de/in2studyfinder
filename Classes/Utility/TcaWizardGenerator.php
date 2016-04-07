<?php
namespace In2code\In2studyfinder\Utility;

/**
 * Class TcaWizardGenerator
 *
 * @package In2code\In2studyfinder\Utility
 */
class TcaWizardGenerator
{

    /**
     * Gets the Suggest Wizard
     * @return array
     */
    static public function getSuggestWizard()
    {
        return [
            'suggest' => [
                'type' => 'suggest',
            ],
        ];
    }
}
