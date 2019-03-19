<?php
namespace Streamlabs\Entities;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Streamlabs\Entities\Users;

/**
 * Users
 *
 * @ORM\Table(name="user_to_streamer")
 * @ORM\Entity
 */
class UserToStreamer
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="smallint")
     */
    private $id;

    /**
     * @ORM\Column(type="smallint")
     */
    private $streamer_id;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="This field is required", groups={"add_fav_streamer"})
     */
    private $streamer_name;

    /**
     * @ORM\Column(type="string")
     */
    private $streamer_display_name;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updated_at;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @var \Streamlabs\Entities\Users
     * @ORM\Column(type="smallint")
     *
     * @ORM\ManyToOne(targetEntity="Users")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user_id;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getStreamerId()
    {
        return $this->streamer_id;
    }

    /**
     * @param mixed $streamer_id
     */
    public function setStreamerId($streamer_id)
    {
        $this->streamer_id = $streamer_id;
    }

    /**
     * @return mixed
     */
    public function getStreamerName()
    {
        return $this->streamer_name;
    }

    /**
     * @param mixed $streamer_name
     */
    public function setStreamerName($streamer_name)
    {
        $this->streamer_name = $streamer_name;
    }

    /**
     * @return mixed
     */
    public function getStreamerDisplayName()
    {
        return $this->streamer_display_name;
    }

    /**
     * @param mixed $streamer_display_name
     */
    public function setStreamerDisplayName($streamer_display_name)
    {
        $this->streamer_display_name = $streamer_display_name;
    }

    /**
     * @return mixed
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * @param mixed $updated_at
     */
    public function setUpdatedAt($updated_at)
    {
        $this->updated_at = $updated_at;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * @param mixed $created_at
     */
    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;
    }

    /**
     * @return \Streamlabs\Entities\Users
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * @param \Streamlabs\Entities\Users $user_id
     */
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }
}