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

namespace Tobento\Service\Responser\Test;

use PHPUnit\Framework\TestCase;
use Tobento\Service\Responser\ResponseInfo;
use Nyholm\Psr7\Factory\Psr17Factory;

/**
 * ResponseInfoTest
 */
class ResponseInfoTest extends TestCase
{    
    public function testIsInformationalMethod()
    {
        $response = (new Psr17Factory())->createResponse(100);
        
        $this->assertTrue((new ResponseInfo($response))->isInformational());
        
        $response = (new Psr17Factory())->createResponse(200);
        
        $this->assertFalse((new ResponseInfo($response))->isInformational());        
    }
    
    public function testIsSuccessfulMethod()
    {
        $response = (new Psr17Factory())->createResponse(200);
        
        $this->assertTrue((new ResponseInfo($response))->isSuccessful());
        
        $response = (new Psr17Factory())->createResponse(300);
        
        $this->assertFalse((new ResponseInfo($response))->isSuccessful());        
    }
    
    public function testIsRedirectionMethod()
    {
        $response = (new Psr17Factory())->createResponse(300);
        
        $this->assertTrue((new ResponseInfo($response))->isRedirection());
        
        $response = (new Psr17Factory())->createResponse(400);
        
        $this->assertFalse((new ResponseInfo($response))->isRedirection());        
    }
    
    public function testIsClientErrorMethod()
    {
        $response = (new Psr17Factory())->createResponse(400);
        
        $this->assertTrue((new ResponseInfo($response))->isClientError());
        
        $response = (new Psr17Factory())->createResponse(500);
        
        $this->assertFalse((new ResponseInfo($response))->isClientError());        
    }
    
    public function testIsServerErrorMethod()
    {
        $response = (new Psr17Factory())->createResponse(500);
        
        $this->assertTrue((new ResponseInfo($response))->isServerError());
        
        $response = (new Psr17Factory())->createResponse(600);
        
        $this->assertFalse((new ResponseInfo($response))->isServerError());        
    }
    
    public function testIsOkMethod()
    {
        $response = (new Psr17Factory())->createResponse(200);
        
        $this->assertTrue((new ResponseInfo($response))->isOk());
        
        $response = (new Psr17Factory())->createResponse(201);
        
        $this->assertFalse((new ResponseInfo($response))->isOk());        
    }
    
    public function testIsForbiddenMethod()
    {
        $response = (new Psr17Factory())->createResponse(403);
        
        $this->assertTrue((new ResponseInfo($response))->isForbidden());
        
        $response = (new Psr17Factory())->createResponse(404);
        
        $this->assertFalse((new ResponseInfo($response))->isForbidden());        
    }
    
    public function testIsNotFoundMethod()
    {
        $response = (new Psr17Factory())->createResponse(404);
        
        $this->assertTrue((new ResponseInfo($response))->isNotFound());
        
        $response = (new Psr17Factory())->createResponse(403);
        
        $this->assertFalse((new ResponseInfo($response))->isNotFound());        
    }
    
    public function testIsCodeMethod()
    {
        $response = (new Psr17Factory())->createResponse(403);
        
        $this->assertTrue((new ResponseInfo($response))->isCode(404, 403, 405));
        
        $this->assertFalse((new ResponseInfo($response))->isCode(500));
    }    
}