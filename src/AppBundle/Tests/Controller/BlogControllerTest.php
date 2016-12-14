<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Tests\Controller;

use AppBundle\Entity\Post;
use Liip\FunctionalTestBundle\Test\WebTestCase;

/**
 * Functional test for the controllers defined inside BlogController.
 * See http://symfony.com/doc/current/book/testing.html#functional-tests
 *
 * Execute the application tests using this command (requires PHPUnit to be installed):
 *
 *     $ cd your-symfony-project/
 *     $ phpunit -c app
 *
 */
class BlogControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/en/blog/');

        $this->assertCount(
            Post::NUM_ITEMS,
            $crawler->filter('article.post'),
            'The homepage displays the right number of posts.'
        );
    }

    /**
     * @test
     */
    public function shouldSee5Comments()
    {
        $content = $this->fetchContent('/en/blog/posts/lorem-ipsum-dolor-sit-amet-consectetur-adipiscing-elit');

        $this->assertContains('5 comments', $content);

        $crawler = $this->fetchCrawler('/en/blog/posts/lorem-ipsum-dolor-sit-amet-consectetur-adipiscing-elit');

        $this->assertEquals(5, $crawler->filter('.post-comment')->count());
    }
}
