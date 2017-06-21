<?php

use CloudDrop\Provider;
use CloudDrop\Interfaces\Provider as ProviderInterface;
use CloudDrop\Providers\Dropbox\Client as DropboxClient;

class ProviderTest extends PHPUnit_Framework_TestCase
{
    public function test_it_can_initialize_a_dropbox_provider()
    {
        $provider = Provider::init('dropbox', 'not_a_real_token');

        $this->assertInstanceOf(DropboxClient::class, $provider);
        $this->assertInstanceOf(ProviderInterface::class, $provider);
    }
}
