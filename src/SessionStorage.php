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

use Tobento\Service\Session\SessionInterface;

/**
 * SessionStorage
 */
class SessionStorage implements StorageInterface
{
    /**
     * Create a new SessionStorage.
     *
     * @param SessionInterface $session
     */
    public function __construct(
        protected SessionInterface $session
    ) {}

    /**
     * Flash a value with the given key.
     *
     * @param string $key
     * @param mixed $value The value to flash
     * @return void
     */
    public function flash(string $key, mixed $value): void
    {
        $this->session->flash($key, $value);
    }
    
    /**
     * Get flashed value from the given key.
     *
     * @param string $key
     * @param mixed $default The default value to fallback.
     * @return mixed The value or if it does not exist the default
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->session->get($key, $default);
    }
}