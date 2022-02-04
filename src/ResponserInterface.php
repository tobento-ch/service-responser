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

use Tobento\Service\Message\MessagesAware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UriInterface;
use Stringable;

/**
 * ResponserInterface
 */
interface ResponserInterface extends MessagesAware
{
    /**
     * Create a new response.
     *
     * @param int $code
     * @return ResponseInterface
     */
    public function create(int $code = 200): ResponseInterface;
    
    /**
     * Returns the stream factory.
     *
     * @return StreamFactoryInterface
     */
    public function streamFactory(): StreamFactoryInterface;
    
    /**
     * Returns the file responser.
     *
     * @return FileResponserInterface
     */
    public function file(): FileResponserInterface;
    
    /**
     * Returns the response info.
     *
     * @param ResponseInterface $response
     * @return ResponseInfo
     */
    public function info(ResponseInterface $response): ResponseInfo;
    
    /**
     * Redirect response.
     *
     * @param string|Stringable|UriInterface $uri
     * @param int $code
     * @return ResponseInterface
     */
    public function redirect(string|Stringable|UriInterface $uri, int $code = 302): ResponseInterface;
    
    /**
     * Write html into the body response.
     *
     * @param string $html
     * @param int $code
     * @param string $contentType
     * @return ResponseInterface
     */
    public function html(
        string $html,
        int $code = 200,
        string $contentType = 'text/html; charset=utf-8'
    ): ResponseInterface;
    
    /**
     * Write json data into the body response.
     *
     * @param mixed $data
     * @param int $code
     * @return ResponseInterface
     */
    public function json(mixed $data, int $code = 200): ResponseInterface;

    /**
     * Render view and write to the body response.
     *
     * @param string $view
     * @param array $data
     * @param int $code
     * @param string $contentType
     * @return ResponseInterface
     */
    public function render(
        string $view,
        array $data = [],
        int $code = 200,
        string $contentType = 'text/html; charset=utf-8'
    ): ResponseInterface;

    /**
     * Write data into the body response.
     *
     * @param mixed $data
     * @param int $code
     * @return ResponseInterface
     */
    public function write(mixed $data, int $code = 200): ResponseInterface;
    
    /**
     * Response with input data.
     *
     * @param array $input
     * @return static $this
     */
    public function withInput(array $input): static;

    /**
     * Returns the input data.
     *
     * @return array
     */
    public function getInput(): array;
}