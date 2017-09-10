<?php

use PHLAK\CloudDrop\Provider;
use PHLAK\CloudDrop\Interfaces\Provider as ProviderInterface;
use PHLAK\CloudDrop\Providers\Dropbox\Client as DropboxClient;

class ProviderTest extends PHPUnit_Framework_TestCase
{
    public function test_it_can_initialize_a_dropbox_provider()
    {
        $dropbox = Provider::init('dropbox', ['access_token' => 'not_a_real_token']);

        $this->assertInstanceOf(DropboxClient::class, $dropbox);
        $this->assertInstanceOf(ProviderInterface::class, $dropbox);
    }
}
