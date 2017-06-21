<?php

namespace CloudDrop\Interfaces;

interface Provider
{
    /**
     * Upload a file to the Provider
     *
     * @param string $path Path to file to send
     * @param string $destination
     *
     * @return void
     */
    public function upload($path, $destination);

    /**
     * Download a file from the Provider
     *
     * @param string $path Name of file to be retrieved
     *
     * @return string File as a binary string
     */
    public function download($path);

    /**
     * Get file info from the Provider
     *
     * @param string $path Name of file
     *
     * @return obj Jason object of file info
     */
    public function info($path);

    /**
     * Check if a file exists in the Provider
     *
     * @param string $path Name of file to check for existence
     *
     * @return bool
     */
    public function exists($path);

    /**
     * Delete a file from the Provider
     *
     * @param string $path Name of file to be deleted
     *
     * @return bool
     */
    public function delete($path);

    /**
     * List the contents of a directory from the Provider
     *
     * @param string $path Path to directory to list
     *
     * @return array Array of directory content objects
     */
    public function list($path);
}
