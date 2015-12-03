<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use AppBundle\Entity\Employee;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Persistence\ObjectManager;

class CommentRatingsControllerTest extends WebTestCase
{
    public function testInvalidVoteParam()
    {
        $client = static::createClient();

        $crawler = $client->request('POST', '/api/post_comments/rating?comment_id=1&user_id=1&vote=notathing');
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $this->assertContains('Param "vote" invalid', $data['data']);
    }

    public function testInvalidCommentParam()
    {
        // setup the fake repo to return no comment
        $em = $this->getFakeEntityManager();
        $repo = $this->getFakeRepo();
        $repo->expects($this->once())
            ->method('find')
            ->will($this->returnValue(false));

        $em->expects($this->once())
            ->method('getRepository')
            ->will($this->returnValue($repo));

        // setup the client and it's fake Entity Manager
        $client = static::createClient();
        $container = $client->getContainer();
        $container->set('doctrine.orm.default_entity_manager', $em);

        $crawler = $client->request('POST', '/api/post_comments/rating?comment_id=1&user_id=1&vote=up');
        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
        $this->assertContains('Comment not found for id 1', $data['data']);
    }

    public function testValidVote() {
        // setup the fake repo to return the comment
        $em = $this->getFakeEntityManager();
        $comment = $this->getMock(Comment::class);
        $commentRepo = $this->getFakeRepo($comment);
        $commentRepo->expects($this->once())
            ->method('find')
            ->will($this->returnValue(true));

        $ratingRepo = $this->getFakeRepo();

        $ratingRepo
            ->expects($this->once())
            ->method('findBy')
            ->will($this->returnValue(false));


        $em->expects($this->exactly(2))
            ->method('getRepository')
            ->will($this->returnCallback(function ($repo) use ($commentRepo, $ratingRepo) {
                if ($repo === 'AppBundle:Comment')
                    return $commentRepo;
                else
                    return $ratingRepo;
            }));

        // setup the client and it's fake Entity Manager
        $client = static::createClient();
        $container = $client->getContainer();
        $container->set('doctrine.orm.default_entity_manager', $em);

        $crawler = $client->request('POST', '/api/post_comments/rating?comment_id=1&user_id=1&vote=up');
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Vote Added', $data['data']);
    }

    private function getFakeRepo() {
        return $this
            ->getMockBuilder(EntityRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function getFakeEntityManager() {
        return $this
            ->getMockBuilder(ObjectManager::class)
            ->disableOriginalConstructor()
            ->getMock();
    }
}
