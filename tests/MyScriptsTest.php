<?php declare(strict_types = 1);

/**
 * PHP version 7.4
 *
 * @category  PHP
 * @package   Madsoft\Talkbot\Test
 * @author    Gyula Madarasz <gyula.madarasz@gmail.com>
 * @copyright 2020 Gyula Madarasz
 * @license   Copyright (c) All rights reserved.
 * @link      this
 */

namespace Madsoft\Talkbot\Test;

use Madsoft\Library\Crud;
use Madsoft\Library\Session;
use Madsoft\Library\Tester\Test;

/**
 * MyScriptsTest
 *
 * @category  PHP
 * @package   Madsoft\Talkbot\Test
 * @author    Gyula Madarasz <gyula.madarasz@gmail.com>
 * @copyright 2020 Gyula Madarasz
 * @license   Copyright (c) All rights reserved.
 * @link      this
 */
class MyScriptsTest extends Test
{
    protected Session $session;
    protected Crud $crud;
     
    /**
     * Method __construct
     *
     * @param Session $session session
     * @param Crud    $crud    crud
     */
    public function __construct(Session $session, Crud $crud)
    {
        $this->session = $session;
        $this->crud = $crud;
    }
    
    /**
     * Method testMyScripts
     *
     * @return void
     *
     * @suppress PhanUnreferencedPublicMethod
     */
    public function testMyScripts(): void
    {
        $this->session->set('uid', 1);
        
        $this->canSeeCreate();
        $this->canSeeCreateWorks();
        
        $this->session->set('uid', 2);
        
        $this->canSeeUser2Create();
        $this->canSeeUser2CreateWorks();
        $this->canSeeUser2ListWorks();
        
        $this->session->set('uid', 1);
        
        $this->canSeeListWorks();
        
        $this->canSeeEditWorks();
        
        $this->session->set('uid', 0);
    }
    
    /**
     * Method canSeeCreate
     *
     * @return void
     */
    protected function canSeeCreate(): void
    {
        $result = $this->get('q=my-scripts/create');
        $this->assertStringContains('My Scripts / Create', $result);
    }
    
    /**
     * Method canSeeCreateWorks
     *
     * @return void
     */
    protected function canSeeCreateWorks(): void
    {
        $result = $this->post(
            'q=my-scripts/create',
            [
                'csrf' => $this->session->get('csrf'),
                'name' => 'testscript',
            ]
        );
        
        $this->assertStringContains(
            htmlentities('Script "testscript" is created'),
            $result
        );
        
        $scripts = $this->crud->getRows('script', ['id', 'name']);
        $this->assertEquals(1, count($scripts));
        $this->assertEquals('testscript', $scripts[0]['name']);
    }
    
    /**
     * Method canSeeListWorks
     *
     * @return void
     */
    protected function canSeeListWorks(): void
    {
        $result = $this->get('q=my-scripts/list');
        $this->assertStringContains('My Scripts', $result);
        $this->assertStringContains('testscript', $result);
        $this->assertStringNotContains('test2script', $result);
    }
    
    /**
     * Method canSeeUser2Create
     *
     * @return void
     */
    protected function canSeeUser2Create(): void
    {
        $result = $this->get('q=my-scripts/create');
        $this->assertStringContains('My Scripts / Create', $result);
    }
    
    /**
     * Method canSeeUser2CreateWorks
     *
     * @return void
     */
    protected function canSeeUser2CreateWorks(): void
    {
        $result = $this->post(
            'q=my-scripts/create',
            [
                'csrf' => $this->session->get('csrf'),
                'name' => 'test2script',
            ]
        );
        
        $this->assertStringContains(
            htmlentities('Script "test2script" is created'),
            $result
        );
        
        $scripts = $this->crud->getRows('script', ['id', 'name']);
        $this->assertEquals(2, count($scripts));
        // TODO: ordered lists
        //        $this->assertEquals('testscript', $scripts[0]['name']);
        //        $this->assertEquals('test2script', $scripts[1]['name']);
    }
    
    /**
     * Method canSeeUser2ListWorks
     *
     * @return void
     */
    protected function canSeeUser2ListWorks(): void
    {
        $result = $this->get('q=my-scripts/list');
        $this->assertStringContains('My Scripts', $result);
        $this->assertStringContains('test2script', $result);
        $this->assertStringNotContains('testscript', $result);
    }
    
    /**
     * Method canSeeEditWorks
     *
     * @return void
     */
    protected function canSeeEditWorks(): void
    {
        $scriptsBeforeEdit = $this->crud->getOwnedRows('script', ['id', 'name']);
        $count = count($scriptsBeforeEdit);
        $script = $scriptsBeforeEdit[0];
        $contents = $this->post(
            'q=my-scripts/edit',
            [
                'csrf' => $this->session->get('csrf'),
                'script_id' => $script['id'],
                'name' => 'test3scriptMOD',
            ]
        );
        $this->assertStringContains('Script saved', $contents);
        $scriptsAfterEdit = $this->crud->getOwnedRows('script', ['id', 'name']);
        $this->assertEquals($count, count($scriptsAfterEdit));
        $this->assertEquals($script['id'], $scriptsAfterEdit[0]['id']);
        $this->assertEquals('test3scriptMOD', $scriptsAfterEdit[0]['name']);
    }
}
