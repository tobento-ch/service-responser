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

namespace Tobento\Service\Responser\Middleware;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Tobento\Service\Responser\ResponserInterface;

/**
 * Adds ResponserInterface to the request attributes.
 */
class Responser implements MiddlewareInterface
{
    /**
     * Create a new Responser middleware.
     *
     * @param ResponserInterface $responser
     */
    public function __construct(
        protected ResponserInterface $responser
    ) {}
    
    /**
     * Process an incoming server request.
     *
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $request = $request->withAttribute(ResponserInterface::class, $this->responser);
        
        return $handler->handle($request);
    }
}