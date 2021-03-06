<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Tests\Controller\Admin;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Functional test for the controllers defined inside the BlogController used
 * for managing the blog in the backend.
 * See http://symfony.com/doc/current/book/testing.html#functional-tests
 *
 * Whenever you test resources protected by a firewall, consider using the
 * technique explained in:
 * http://symfony.com/doc/current/cookbook/testing/http_authentication.html
 *
 * Execute the application tests using this command (requires PHPUnit to be installed):
 *
 *     $ cd your-symfony-project/
 *     $ phpunit -c app
 *
 */
class BlogControllerTest extends WebTestCase
{
    protected function setUp()
    {
        $fixture = array(
            'AppBundle\DataFixtures\ORM\LoadFixtures'
        );

        $this->loadFixtures($fixture);
    }

    public function testRegularUsersCannotAccessToTheBackend()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'john_user',
            'PHP_AUTH_PW'   => 'kitten',
        ));

        $client->request('GET', '/en/admin/post/');

        $this->assertEquals(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());
    }

    public function testAdministratorUsersCanAccessToTheBackend()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'anna_admin',
            'PHP_AUTH_PW'   => 'kitten',
        ));

        $client->request('GET', '/en/admin/post/');

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    public function testIndex()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'anna_admin',
            'PHP_AUTH_PW'   => 'kitten',
        ));

        $crawler = $client->request('GET', '/en/admin/post/');

        $this->assertCount(
            30,
            $crawler->filter('body#admin_post_index #main tbody tr'),
            'The backend homepage displays all the available posts.'
        );
    }

    /**
     * @test
     */
    public function shouldEditAPost()
    {
        $postId = 1;

        $credentials = array(
            'username' => 'anna_admin',
            'password' => 'kitten'
        );

        $client = $this->makeClient($credentials);

        $url = $this->getUrl('admin_post_edit', array('id' => $postId));
        $crawler = $client->request('GET', $url);
        $this->assertStatusCode(200, $client);

        $form = $crawler->selectButton('Save changes')->form();
        $crawler = $client->submit($form);

        $form->setValues(
            array(
                'post[title]' => 'title molon',
            )
        );
        $client->submit($form);
        $this->assertStatusCode(302, $client);
    }

    /**
     * @test
     */
    public function shouldCreateAPost()
    {
        $credentials = array(
            'username' => 'anna_admin',
            'password' => 'kitten'
        );

        $client = $this->makeClient($credentials);

        $url = $this->getUrl('admin_post_new');
        $crawler = $client->request('GET', $url);
        $this->assertStatusCode(200, $client);

        $form = $crawler->selectButton('Create post')->form();
        $crawler = $client->submit($form);

        $form->setValues(
            array(
                'post[title]' => 'title molon',
                'post[summary]' => 'summary mega molon',
                'post[content]' => 'ultra mega molon content',
            )
        );

        $client->submit($form);
        $client->followRedirect();

        $response = $client->getResponse();
        $this->isSuccessful($response);

        $content = $response->getContent();
        $this->assertContains('Post created successfully!', $content);
        $this->assertContains('title molon', $content);
    }

    /**
     * @test
     */
    public function shouldCreateAPostAndGoToEditPage()
    {
        $credentials = array(
            'username' => 'anna_admin',
            'password' => 'kitten'
        );

        $client = $this->makeClient($credentials);

        $url = $this->getUrl('admin_post_new');
        $crawler = $client->request('GET', $url);
        $this->assertStatusCode(200, $client);

        $form = $crawler->selectButton('Save and edit')->form();
        $crawler = $client->submit($form);

        $form->setValues(
            array(
                'post[title]' => 'title molon',
                'post[summary]' => 'summary mega molon',
                'post[content]' => 'ultra mega molon content',
            )
        );

        $client->submit($form);
        $this->assertStatusCode(302, $client);
        $response = $client->getResponse();

        $this->assertRegExp('/\/en\/admin\/post\/([0-9])*\/edit/', $response->getContent());
    }

    /**
     * @test
     */
    public function shouldCreateAComment()
    {
        $credentials = array(
            'username' => 'anna_admin',
            'password' => 'kitten'
        );

        $client = $this->makeClient($credentials);

        $post = $this->getContainer()->get('doctrine')->getRepository('AppBundle:Post')->find(1);

        $url = $this->getUrl('blog_post', array('slug' => $post->getSlug()));
        $crawler = $client->request('GET', $url);
        $this->assertStatusCode(200, $client);

        $form = $crawler->selectButton('Publish comment')->form();
        $crawler = $client->submit($form);

        $form->setValues(
            array(
                'comment[content]' => 'comentario muy molon',
            )
        );

        $client->submit($form);
        $this->assertStatusCode(302, $client);
        $client->followRedirect();

        $response = $client->getResponse();
        $this->isSuccessful($response);

        $content = $response->getContent();
        $this->assertContains('comentario muy molon', $content);
    }

    /**
     * @test
     */
    public function shouldGetErrorNoContentWhenCreateAPost()
    {
        $credentials = array(
            'username' => 'anna_admin',
            'password' => 'kitten'
        );

        $client = $this->makeClient($credentials);

        $url = $this->getUrl('admin_post_new');
        $crawler = $client->request('GET', $url);
        $this->assertStatusCode(200, $client);

        $form = $crawler->selectButton('Create post')->form();
        $crawler = $client->submit($form);

        $form->setValues(
            array(
                'post[title]' => 'title molon',
                'post[summary]' => 'summary mega molon',
            )
        );

        $client->submit($form);

        $response = $client->getResponse();
        $this->isSuccessful($response);

        $this->assertContains('Your post should have some content!', $response->getContent());
    }
}
