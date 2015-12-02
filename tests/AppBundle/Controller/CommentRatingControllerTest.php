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

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $this->assertContains('Param "vote" invalid', $crawler->text());
    }

    public function testInvalidCommentParam()
    {
        // setup the fake repo to return no comment
        $em = $this->getFakeEntityManager();
        $repo = $this->getFakeCommentRepo(false);
        $em->expects($this->once())
            ->method('getRepository')
            ->will($this->returnValue($repo));

        // setup the client and it's fake Entity Manager
        $client = static::createClient();
        $container = $client->getContainer();
        $container->set('doctrine.orm.default_entity_manager', $em);

        $crawler = $client->request('POST', '/api/post_comments/rating?comment_id=1&user_id=1&vote=up');

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
        $this->assertContains('No comment found for id 1', $crawler->text());
    }

    private function getFakeComment() {
        $comment = $this->getMock(Comment::class);
    }

    private function getFakeCommentRepo($value) {
        $commentRepository = $this
            ->getMockBuilder(EntityRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $commentRepository->expects($this->once())
            ->method('find')
            ->will($this->returnValue($value));

        return $commentRepository;
    }

    private function getFakeEntityManager() {
        return $this
            ->getMockBuilder(ObjectManager::class)
            ->disableOriginalConstructor()
            ->getMock();
    }
}
