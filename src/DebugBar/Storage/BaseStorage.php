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
 * Base storage class
 */
abstract class BaseStorage
{
    protected $gc_lifetime = 86400;  // Seconds to keep collected data (24 hours);
    protected $gc_probability = 5;   // Probability of GC being run on a save request. (5/100)

    /**
     * {@inheritDoc}
     *
     */
    public function setGcLifetime($lifetime){
        $this->gc_lifetime = $lifetime;
    }

    /**
     * {@inheritDoc}
     *
     */
    public function setGcProbability($probability){
        $this->gc_probability = $probability;
    }

    /**
     * {@inheritDoc}
     *
     */
    public function checkGc(){
        if(rand(1, 100) <= $this->gc_probability){
            $this->gc($this->gc_lifetime);
            return true;
        }
        return false;
    }

}
