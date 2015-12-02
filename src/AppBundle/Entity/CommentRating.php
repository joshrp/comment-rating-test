<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="comment_ratings",
 *   indexes={@ORM\Index(name="comment_idx", columns={"comment_id"})})
 * @ORM\Entity(repositoryClass="CommentRatingsRepository")
 */
class CommentRating {
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $user_id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $comment_id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $value;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     *
     * @return Comment
     */
    public function setUserId($userId)
    {
        $this->user_id = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return integer
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * Set value
     *
     * @param integer $value
     *
     * @return Comment
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return integer
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set commentId
     *
     * @param integer $commentId
     *
     * @return CommentRating
     */
    public function setCommentId($commentId)
    {
        $this->comment_id = $commentId;

        return $this;
    }

    /**
     * Get commentId
     *
     * @return integer
     */
    public function getCommentId()
    {
        return $this->comment_id;
    }
}
