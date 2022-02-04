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
 * Merges the responser input with the request input.
 */
class ResponserMergeInput implements MiddlewareInterface
{
    /**
     * @var array The request methods to merge input.
     */
    protected array $mergeInputMethods = ['POST', 'PUT', 'PATCH'];
    
    /**
     * Create a new ResponserMergeInput middleware.
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
        if (empty($this->responser->getInput())) {
            return $handler->handle($request);
        }
        
        if (in_array($request->getMethod(), ['HEAD', 'GET', 'OPTIONS'])) {
            $request = $request->withQueryParams(array_merge(
                $this->responser->getInput(),
                $request->getQueryParams()
            ));
            
            return $handler->handle($request);
        }
        
        $parsedBody = $request->getParsedBody();

        if (!is_array($parsedBody)) {
            return $handler->handle($request);
        }
        
        $request = $request->withParsedBody(array_merge(
            $this->responser->getInput(),
            $parsedBody
        ));

        return $handler->handle($request);
    }
}