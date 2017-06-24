<?php

namespace CloudDrop\Providers\Dropbox;

use CloudDrop\Interfaces\Provider;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ClientException;

class Client implements Provider
{
    /** @var string Dropbox API access token */
    protected $accessToken;

    /**
     * Providers\Dropbox\Client constructor. Runs on object creation.
     *
     * @param string $accessToken Dropbox API access token
     */
    function __construct($accessToken)
    {
        $this->accessToken = $accessToken;
    }

    /**
     * Upload a file to Dropbox.
     *
     * @param string $path Path to file to be uploaded
     * @param string $destination Path/name of resulting file in Dropbox
     *
     * @return object Uploaded file metadata object
     */
    public function upload($path, $destination = null)
    {
        $fileSize = filesize($path);
        $destination = $this->path($destination ?? basename($path));

        if ($fileSize <= 150000000) {
            return $this->simpleUpload($path, $destination);
        }

        return $this->chunkedUpload($path, $fileSize, $destination);
    }

    /**
     * Download a file from Dropbox.
     *
     * @param string $path Path to file in Dropbox
     *
     * @return File Dropbox\File object
     */
    public function download($path)
    {
        $response = $this->content()->post('files/download', [
            'headers' => [
                'Dropbox-API-Arg' => json_encode([
                    'path' => $this->path($path)
                ])
            ]
        ]);

        // TODO: Throw FileNotFoundException when no file is located

        return new File(
            $response->getBody()->getContents(),
            json_decode($response->getHeader('dropbox-api-result')[0])
        );
    }

    /**
     * Retrieve info (metadata) for a file in Dropbox.
     *
     * @param string $path Path to file in Dropbox
     *
     * @return object File metadata object
     */
    public function info($path, $deleted = false)
    {
        $response = $this->api()->post('files/get_metadata', [
            'json' => [
                'path' => $this->path($path),
                'include_media_info' => false,
                'include_deleted' => $deleted
            ]
        ]);

        // TODO: Throw FileNotFoundException when no file is located

        return json_decode($response->getBody());
    }

    /**
     * Check if a file exists in Dropbox.
     *
     * @param string $path Path to file in Dropbox
     *
     * @return bool True if file exists in Dropbox, otherwise false
     */
    public function exists($path)
    {
        try {
            $this->info($path);
        } catch (ClientException $exception) {
            return false;
        }

        return true;
    }

    /**
     * Delete a file from Dropbox.
     *
     * @param string $path Path to file in Dropbox
     *
     * @return object Deleted file metadata object
     */
    public function delete($path)
    {
        $response = $this->api()->post('files/delete', [
            'json' => [
                'path' => $this->path($path)
            ]
        ]);

        // TODO: Throw FileNotFoundException when no file is located

        return json_decode($response->getBody());
    }

    /**
     * List the contents of a directory in Dropbox.
     *
     * @param string $path Path of directory to list
     * @param bool $recursive If true, list directories recursively
     * @param bool $deleted If true, include deleted files and folders
     *
     * @return array Array of directory content objects
     */
    public function list($path = '', $recursive = false, $deleted = false)
    {
        $response = $this->api()->post('files/list_folder', [
            'json' => [
                'path' => $this->path($path),
                'recursive' => $recursive,
                'include_media_info' => false,
                'include_deleted' => false
            ]
        ]);

        $body = json_decode($response->getBody());
        $entries = $body->entries;

        while ($body->has_more) {
            $body = $this->listContinue($body->cursor);
            $entries = array_merge($entries, $body->entries);
        }

        return $entries;
    }

    /**
     * Get the Dropbox API RPC endpoint client
     *
     * @return GuzzleHttp\Client GuzzleHttp client instance
     */
    protected function api()
    {
        return new GuzzleClient([
            'base_uri' => 'https://api.dropboxapi.com/2/',
            'headers' => [
                'Authorization' => 'Bearer ' . $this->accessToken
            ]
        ]);
    }

    /**
     * Get the Dropbox API Content client
     *
     * @return GuzzleHttp\Client GuzzleHttp client instance
     */
    protected function content()
    {
        return new GuzzleClient([
            'base_uri' => 'https://content.dropboxapi.com/2/',
            'headers' => [
                'Authorization' => 'Bearer ' . $this->accessToken
            ]
        ]);
    }

    /**
     * Upload a file less than or equal to 150MB in a single transaction.
     *
     * @param string $path Path to file to be uploaded
     * @param string $destination Path/name of resulting file in Dropbox
     *
     * @return object Uploaded file metadata object
     */
    protected function simpleUpload($path, $destination)
    {
        $response = $this->content()->post('files/upload', [
            'headers' => [
                'Content-Type' => 'application/octet-stream',
                'Dropbox-API-Arg' => json_encode([
                    'path' => $destination,
                    'mode' => 'add',
                    'autorename' => true,
                    'mute' => false
                ])
            ],
            'body' => file_get_contents($path)
        ]);

        return json_decode($response->getBody());
    }

    /**
     * Upload a file in multiple chunks. Required for files over 150MB in size.
     *
     * @param string $path Path to file to be uploaded
     * @param int $fileSize Size of file in bytes
     * @param string $destination Path/name of resulting file in Dropbox
     *
     * @return object Upload session object
     */
    protected function chunkedUpload($path, $fileSize, $destination)
    {
        $upload = $this->uploadStart();

        for ($offset = 0; $offset <= $fileSize; $offset += 150000000) {
            $chunk = file_get_contents($path, false, null, $offset, 150000000);
            $this->uploadAppend($upload->session_id, $chunk, $offset);
        }

        return $this->uploadFinish($upload->session_id, $fileSize, $destination);
    }

    /**
     * Start a chunked upload session.
     *
     * @return object Upload session object
     */
    protected function uploadStart()
    {
        $response = $this->content()->post('files/upload_session/start', [
            'headers' => [
                'Content-Type' => 'application/octet-stream'
            ]
        ]);

        return json_decode($response->getBody()->getContents());
    }

    /**
     * Upload a chunk of data belonging to an upload session.
     *
     * @param string $sessionId Upload session ID
     * @param string $chunk String of chunk data, should not exceed 150MB
     * @param int $offset Offset for the chunk being appended
     *
     * @return object Upload session object
     */
    protected function uploadAppend($sessionId, $chunk, $offset)
    {
        $this->content()->post('files/upload_session/append_v2', [
            'headers' => [
                'Content-Type' => 'application/octet-stream',
                'Dropbox-API-Arg' => json_encode([
                    'cursor' => [
                        'session_id' => $sessionId,
                        'offset' => $offset
                    ]
                ])
            ],
            'body' => $chunk
        ]);
    }

    /**
     * Close a chuncked upload session.
     *
     * @param string $sessionId Upload session ID
     * @param int $fileSize Size of file in bytes
     * @param string $destination Path/name of resulting file in Dropbox
     *
     * @return object Uploaded file metadata object
     */
    protected function uploadFinish($sessionId, $fileSize, $destination)
    {
        $response = $this->content()->post('files/upload_session/finish', [
            'headers' => [
                'Content-Type' => 'application/octet-stream',
                'Dropbox-API-Arg' => json_encode([
                    'cursor' => [
                        'session_id' => $sessionId,
                        'offset' => $fileSize
                    ],
                    'commit' => [
                        'path' => $destination,
                        'mode' => 'add',
                        'autorename' => false,
                        'mute' => false
                    ]
                ])
            ]
        ]);

        return json_decode($response->getBody());
    }

    /**
     * Get more contents from a directory
     *
     * @param string $cursor Current cursor supplied by list_folder request
     *
     * @return object Folder listing object
     */
    protected function listContinue($cursor) {
        $response = $this->api()->post('files/list_folder/continue', [
            'json' => ['cursor' => $cursor]
        ]);

        return json_decode($response->getBody());
    }

    /**
     * Ensure proper formatting of a file path string
     *
     * @param string $path File path in Dropbox
     *
     * @return string Properly formatted file name
     */
    protected function path($path)
    {
        if (empty($path) || preg_match('/^id:.*$/', $path)) {
            return $path;
        }

        return '/' . ltrim($path, '/');
    }
}
