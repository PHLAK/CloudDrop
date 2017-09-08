<?php

namespace PHLAK\CloudDrop;

class Provider
{
    /** @var Provider An instance of the Provider interface */
    protected static $clients = [
        'dropbox' => Providers\Dropbox\Client::class,
        // 'google_drive' => Providers\GoogleDrive\Client::class,
        // 'one_drive' => Providers\MicrosoftOneDrive\Client::class,
        // 'cloud_drive' => Providers\AmazonCloudDrive\Client::class
    ];

    /**
     * Initialize a Provider.
     *
     * @param string $provider    Provider string
     * @param string $accessToken Provider API access token
     *
     * @return Interfaces\Client Instance of a Provider client
     */
    public static function init($provider, $accessToken)
    {
        return new self::$clients[$provider]($accessToken);
    }
}
