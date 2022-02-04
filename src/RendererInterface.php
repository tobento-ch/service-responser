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
 * RendererInterface
 */
interface RendererInterface
{
    /**
     * Render the view
     *
     * @param string $view The view name.
     * @param array $data View data.
     * @return string The view rendered.
     */
    public function render(string $view, array $data = []): string;
}