<?php

namespace PHLAK\CloudDrop\Providers\Dropbox;

use PHLAK\CloudDrop\Interfaces\File as FileInterface;

class File implements FileInterface
{
    /** @var string Raw file contents */
    protected $contents;

    /** @var object Metadata JSON object */
    protected $metadata;

    /**
     * Dropbox File constructor object. Runs on object creation.
     *
     * @param string $contents String of file contents
     * @param object $metadata JSON object of file metadata
     */
    public function __construct($contents, $metadata)
    {
        $this->contents = $contents;
        $this->metadata = $metadata;
    }

    /**
     * Magig getter method.
     *
     * @param string $property Property name
     *
     * @return mixed
     */
    public function __get($property)
    {
        return $this->$property();
    }

    /**
     * Magic string handler method.
     *
     * @return string File contents as a string
     */
    public function __toString()
    {
        return $this->contents;
    }

    /**
     * Get the raw File contents as a string.
     *
     * @return string File contents
     */
    public function contents()
    {
        return $this->contents;
    }

    /**
     * Get the File metadata.
     *
     * @return object JSON object of File metadata
     */
    public function metadata()
    {
        return $this->metadata;
    }

    /**
     * Get the File ID.
     *
     * @return string File ID
     */
    public function id()
    {
        return $this->metadata()->id;
    }

    /**
     * Get the File name.
     *
     * @return string File name
     */
    public function name()
    {
        return $this->Metadata()->name;
    }

    /**
     * Get the file size.
     *
     * @return int File size in bytes
     */
    public function size()
    {
        return $this->metadata()->size;
    }

    /**
     * Create the File at a specific path.
     *
     * @param string $path Path of file to be created
     *
     * @return int|false Number of bytes that written or FALSE on failure
     */
    public function to($path)
    {
        return file_put_contents($path, $this->contents, LOCK_EX);
    }
}
