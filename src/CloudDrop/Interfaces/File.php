<?php

namespace CloudDrop\Interfaces;

interface File
{
    /**
     * Get the raw File contents as a string
     *
     * @return string File contents
     */
    public function contents();

    /**
     * Get the File metadata object
     *
     * @return object JSON object of File metadata
     */
    public function metadata();

    /**
     * Get the File ID
     *
     * @return string File ID
     */
    public function id();

    /**
     * Get the File name
     *
     * @return string File name
     */
    public function name();

    /**
     * Get the file size in bytes
     *
     * @return int File size in bytes
     */
    public function size();

    /**
     * Create the File at a specific path
     *
     * @return void
     */
    public function to($path);
}
