<?php

/**
 * TOBENTO
 *
 * @copyright   Tobias Strub, TOBENTO
 * @license     MIT License, see LICENSE file distributed with this source code.
 * @author      Tobias Strub
 * @link        https://www.tobento.ch
 */

declare(strict_types=1);

namespace Tobento\Service\Responser;

/**
 * StorageInterface
 */
interface StorageInterface
{
    /**
     * Flash a value with the given key.
     *
     * @param string $key
     * @param mixed $value The value to flash.
     * @return void
     */
    public function flash(string $key, mixed $value): void;
    
    /**
     * Get flashed value from the given key.
     *
     * @param string $key
     * @param mixed $default The default value if not found.
     * @return mixed The default value to fallback.
     */
    public function get(string $key, mixed $default = null): mixed;
}