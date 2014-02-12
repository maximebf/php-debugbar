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

interface StorageInterface
{
    /**
     * Saves collected data
     *
     * @param string $id
     * @param string $data
     */
    function save($id, $data);

    /**
     * Returns collected data with the specified id
     *
     * @param string $id
     * @return array
     */
    function get($id);

    /**
     * Returns a metadata about collected data
     *
     * @param array $filters
     * @param integer $max
     * @param integer $offset
     * @return array
     */
    function find(array $filters = array(), $max = 20, $offset = 0);

    /**
     * Check if the gc should be run
     *
     */
    function checkGc();

    /**
     * Delete collected data older than a certain lifetime
     *
     * @param int $lifetime in seconds
     */
    function gc($lifetime);

    /**
     * Set the lifetime of the datasets in seconds.
     *
     * @param int $lifetime in seconds
     */
    function setGcLifetime($lifetime);

    /**
     * Set the Gc probability to run on a request.
     *
     * @param int $probability Percentage
     */
    function setGcProbability($probability);

    /**
     * Clears all the collected data
     */
    function clear();
}
