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

use Tobento\Service\View\ViewInterface;

/**
 * ViewRenderer
 */
class ViewRenderer implements RendererInterface
{
    /**
     * Create a new ViewRenderer.
     *
     * @param ViewInterface $view
     */
    public function __construct(
        protected ViewInterface $view
    ) {}

    /**
     * Render the view.
     *
     * @param string $view The view name.
     * @param array $data View data.
     * @return string The view rendered.
     */
    public function render(string $view, array $data = []): string
    {
        return $this->view->render($view, $data);
    }
}