<?php
namespace Streamlabs\Entities;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Streamlabs\Entities\Users;

/**
 * StreamersEventLog
 *
 * @ORM\Table(name="streamers_event_log")
 * @ORM\Entity
 */
class StreamersEventLog
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="smallint")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $event_type;

    /**
     * @ORM\Column(type="text")
     */
    private $event_data;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @var \Streamlabs\Entities\UserToStreamer
     * @ORM\Column(type="smallint")
     *
     * @ORM\ManyToOne(targetEntity="UserToStreamer")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="streamer_id", referencedColumnName="streamer_id")
     * })
     */
    private $streamer_id;

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
    public function getEventType()
    {
        return $this->event_type;
    }

    /**
     * @param mixed $event_type
     */
    public function setEventType($event_type)
    {
        $this->event_type = $event_type;
    }

    /**
     * @return mixed
     */
    public function getEventData()
    {
        return $this->event_data;
    }

    /**
     * @param mixed $event_data
     */
    public function setEventData($event_data)
    {
        $this->event_data = $event_data;
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
     * @return UserToStreamer
     */
    public function getStreamerId()
    {
        return $this->streamer_id;
    }

    /**
     * @param UserToStreamer $streamer_id
     */
    public function setStreamerId($streamer_id)
    {
        $this->streamer_id = $streamer_id;
    }
}