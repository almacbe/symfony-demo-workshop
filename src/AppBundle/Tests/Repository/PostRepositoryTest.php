<?php

namespace AppBundle\Tests\Repository;

use AppBundle\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Liip\FunctionalTestBundle\Test\WebTestCase;

class PostRepositoryTest extends WebTestCase
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        self::bootKernel();
        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager()
        ;
    }

    /**
     * @test
     */
    public function shouldNotGetPostOfTheGivenMonth()
    {
        /** @var PostRepository $repository */
        $repository = $this->em->getRepository('AppBundle:Post');

        $month = new \DateTime('-5 month');
        $posts = $repository->findPublishOn($month);

        $this->assertEmpty($posts);
    }

    /**
     * @test
     */
    public function shouldGetPostOfTheGivenMonth()
    {
        $fixture = array(
            'AppBundle\DataFixtures\ORM\LoadPosts'
        );

        $this->loadFixtures($fixture);

        /** @var PostRepository $repository */
        $repository = $this->em->getRepository('AppBundle:Post');

        $month = new \DateTime('-1 month');
        $posts = $repository->findPublishOn($month);

        $this->assertCount(1, $posts);
    }

    protected function tearDown()
    {
        parent::tearDown();

        $this->em->close();
        $this->em = null; // avoid memory leaks
    }
}
