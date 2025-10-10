<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Service;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;

class FeSessionService
{
    public function saveToSession(string $key, $value, ServerRequestInterface $request): void
    {
        $frontendUser = $request->getAttribute('frontend.user');
        if (!$frontendUser instanceof FrontendUserAuthentication) {
            return;
        }

        $frontendUser->setKey('ses', $key, $value);
        $frontendUser->storeSessionData();
    }

    public function getFromSession(string $key, ServerRequestInterface $request)
    {
        $frontendUser = $request->getAttribute('frontend.user');
        if (!$frontendUser instanceof FrontendUserAuthentication) {
            return null;
        }

        return $frontendUser->getKey('ses', $key);
    }

    public function getSessionIdentifier(ServerRequestInterface $request): ?string
    {
        $frontendUser = $request->getAttribute('frontend.user');
        if (!$frontendUser instanceof FrontendUserAuthentication) {
            return null;
        }

        return $frontendUser->getSession()->getIdentifier();
    }
}
