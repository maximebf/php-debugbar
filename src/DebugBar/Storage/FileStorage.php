<?php
/*
 * This file is part of the DebugBar package.
 *
 * (c) 2013 Maxime Bouroumeau-Fuseau
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DebugBar\Storage;

/**
 * Stores collected data into files
 */
class FileStorage implements StorageInterface
{
    protected $dirname;

    /**
     * @param string $dirname Directories where to store files
     */
    public function __construct($dirname)
    {
        $this->dirname = rtrim($dirname, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    }

    /**
     * {@inheritDoc}
     */
    public function save($id, $data)
    {
        file_put_contents($this->makeFilename($id), json_encode($data));
    }

    /**
     * {@inheritDoc}
     */
    public function get($id)
    {
        return json_decode(file_get_contents($this->makeFilename($id)), true);
    }

    /**
     * {@inheritDoc}
     */
    public function find(array $filters = array(), $max = 20, $offset = 0)
    {
        $results = array();
        foreach (new \DirectoryIterator($this->dirname) as $file) {
            if (substr($file->getFilename(), 0, 1) !== '.') {
                $id = substr($file->getFilename(), 0, strpos($file->getFilename(), '.'));
                $data = $this->get($id);
                $meta = $data['__meta'];
                unset($data);
                if (array_keys(array_intersect($meta, $filters)) == array_keys($filters)) {
                    $results[] = $meta;
                }
            }
        }
        return array_slice($results, $offset, $max);
    }

    /**
     * {@inheritDoc}
     */
    public function clear()
    {
        foreach (new \DirectoryIterator($this->dirname) as $file) {
            if (substr($file->getFilename(), 0, 1) !== '.') {
                unlink($file->getPathname());
            }
        }
    }

    public function makeFilename($id)
    {
        return $this->dirname . "$id.json";
    }
}