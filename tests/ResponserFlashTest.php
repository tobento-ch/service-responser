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
use Tobento\Service\Responser\Responser;
use Tobento\Service\Responser\ResponserInterface;
use Tobento\Service\Responser\StorageInterface;
use Tobento\Service\Responser\SessionStorage;
use Tobento\Service\Session\Session;
use Tobento\Service\Session\SessionStartException;
use Tobento\Service\Session\SessionSaveException;
use Nyholm\Psr7\Factory\Psr17Factory;

/**
 * ResponserFlashTest
 */
class ResponserFlashTest extends TestCase
{
    protected function createResponser(StorageInterface $storage): ResponserInterface
    {
        $psr17Factory = new Psr17Factory();
        
        return new Responser(
            responseFactory: $psr17Factory,
            streamFactory: $psr17Factory,
            storage: $storage,
        );
    }
    
    public function testFlashInput()
    {
        $session = new Session('name');
        $storage = new SessionStorage($session);
        
        // current request        
        try {
            $session->start();
        } catch (SessionStartException $e) {
            //
        }

        $responser = $this->createResponser($storage);
        
        $responser->withInput(['key' => 'value'])->redirect('url');
        
        $this->assertSame('value', $responser->getInput()['key'] ?? null);
        
        try {
            $session->save();
        } catch (SessionSaveException $e) {
            //
        }
        
        // next request
        $responser = $this->createResponser($storage);
        
        try {
            $session->start();
        } catch (SessionStartException $e) {
            //
        }
        
        $this->assertSame('value', $responser->getInput()['key'] ?? null);
        
        try {
            $session->save();
        } catch (SessionSaveException $e) {
            //
        }
        
        // after next request
        $responser = $this->createResponser($storage);
        
        try {
            $session->start();
        } catch (SessionStartException $e) {
            //
        }
        
        $this->assertSame(null, $responser->getInput()['key'] ?? null);
        
        try {
            $session->save();
        } catch (SessionSaveException $e) {
            //
        }        
    }
    
    public function testFlashMessages()
    {
        $session = new Session('name');
        $storage = new SessionStorage($session);
        
        // current request        
        try {
            $session->start();
        } catch (SessionStartException $e) {
            //
        }

        $responser = $this->createResponser($storage);
        $responser->messages()->add('error', 'Message');
        $responser->redirect('url');
        
        $this->assertSame(1, count($responser->messages()->all()));
        
        try {
            $session->save();
        } catch (SessionSaveException $e) {
            //
        }
        
        // next request
        $responser = $this->createResponser($storage);
        
        try {
            $session->start();
        } catch (SessionStartException $e) {
            //
        }
        
        $this->assertSame(1, count($responser->messages()->all()));
        
        try {
            $session->save();
        } catch (SessionSaveException $e) {
            //
        }
        
        // after next request
        $responser = $this->createResponser($storage);
        
        try {
            $session->start();
        } catch (SessionStartException $e) {
            //
        }
        
        $this->assertSame(0, count($responser->messages()->all()));
        
        try {
            $session->save();
        } catch (SessionSaveException $e) {
            //
        }        
    }    
}