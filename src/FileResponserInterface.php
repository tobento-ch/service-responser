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

use Tobento\Service\Filesystem\File;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use InvalidArgumentException;

/**
 * FileResponserInterface
 */
interface FileResponserInterface
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
     * Returns the response info.
     *
     * @param ResponseInterface $response
     * @return ResponseInfo
     */
    public function info(ResponseInterface $response): ResponseInfo;  
    
    /**
     * Download file response.
     *
     * @param mixed $file string|File|StreamInterface|resource
     * @param string $name
     * @param string $contentType
     * @return ResponseInterface
     *
     * @throws InvalidArgumentException
     */
    public function download(
        mixed $file,
        string $name = '',
        null|string $contentType = null
    ): ResponseInterface;
    
    /**
     * Render file response.
     *
     * @param mixed $file string|File|StreamInterface|resource
     * @param string $name
     * @param null|string $contentType
     * @return ResponseInterface
     *
     * @throws InvalidArgumentException
     */
    public function render(
        mixed $file,
        string $name = '',
        null|string $contentType = null
    ): ResponseInterface;
}