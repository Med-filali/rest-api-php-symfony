<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RequestStack;
/**
 * Creation of a Symfony service to be able to retrieve the version contained in the "accept" field of the HTTP request.
 */
class VersioningService
{
    private $requestStack;

    /**
     * @param RequestStack $requestStack
     * @param ParameterBagInterface $params
     */
    public function __construct(RequestStack $requestStack, ParameterBagInterface $params)
    {
        $this->requestStack = $requestStack;
        $this->defaultVersion = $params->get('default_api_version');
    }

    /**
     * Retrieval of the version that was sent in the "accept" header of the HTTP request
     *
     * @return string
     */
    public function getVersion(): string
    {
        $version = $this->defaultVersion;

        $request = $this->requestStack->getCurrentRequest();
        $accept = $request->headers->get('Accept');
        // Retrieval of the version number in the accept character string:
        // example "application/json; test=bidule; version=2.0"
        $entete = explode(';', $accept);

        // We go through all the headers to find the version
        foreach ($entete as $value) {
            if (strpos($value, 'version') !== false) {
                $version = explode('=', $value);
                $version = $version[1];
                break;
            }
        }
        return $version;
    }
}