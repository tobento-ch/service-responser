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

use Psr\Http\Message\ResponseInterface;

/**
 * ResponseInfo
 */
class ResponseInfo
{
    /**
     * Create a new ResponseInfo.
     *
     * @param ResponseInterface $response
     */
    public function __construct(
        protected ResponseInterface $response
    ) {}

    /**
     * If the response is informational.
     *
     * @return bool
     */
    public function isInformational(): bool
    {
        $code = $this->response->getStatusCode();
        
        return $code >= 100 && $code < 200;
    }
    
    /**
     * If the response is successful.
     *
     * @return bool
     */
    public function isSuccessful(): bool
    {
        $code = $this->response->getStatusCode();
        
        return $code >= 200 && $code < 300;
    }

    /**
     * If the response is a redirection.
     *
     * @return bool
     */
    public function isRedirection(): bool
    {
        $code = $this->response->getStatusCode();
        
        return $code >= 300 && $code < 400;
    }

    /**
     * If the response is a client error.
     *
     * @return bool
     */
    public function isClientError(): bool
    {
        $code = $this->response->getStatusCode();
        
        return $code >= 400 && $code < 500;
    }
    
    /**
     * If the response is a server error.
     *
     * @return bool
     */
    public function isServerError(): bool
    {
        $code = $this->response->getStatusCode();
        
        return $code >= 500 && $code < 600;
    }
    
    /**
     * If the response is ok.
     *
     * @return bool
     */
    public function isOk(): bool
    {
        return $this->response->getStatusCode() === 200;
    }
    
    /**
     * If the response is a forbidden error.
     *
     * @return bool
     */
    public function isForbidden(): bool
    {
        return $this->response->getStatusCode() === 403;
    }
    
    /**
     * If the response is a not found error.
     *
     * @return bool
     */
    public function isNotFound(): bool
    {
        return $this->response->getStatusCode() === 404;
    }
    
    /**
     * If the response is code.
     *
     * @param int ...$code
     * @return bool
     */
    public function isCode(int ...$code): bool
    {
        return in_array($this->response->getStatusCode(), $code);
    }    
}