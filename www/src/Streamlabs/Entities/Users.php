<?php
namespace TaxSure\Entities;

/**
 * @Entity
 * @Table(name="users")
 */
class Users
{
    /**
     * @Id
     * @GeneratedValue
     * @Column(type="smallint")
     */
    private $id;

    /**
     * @Column(type="smallint")
     */
    private $twitch_id;

    /**
     * @Column(type="string")
     */
    private $twitch_login;

    /**
     * @Column(type="string")
     */
    private $twitch_display_name;

    /**
     * @Column(type="string")
     */
    private $twitch_broadcaster_type;

    /**
     * @Column(type="datetime")
     */
    private $last_login;

    /**
     * @Column(type="timestamp")
     */
    private $created_at;

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
    public function getTwitchId()
    {
        return $this->twitch_id;
    }

    /**
     * @param mixed $twitch_id
     */
    public function setTwitchId($twitch_id)
    {
        $this->twitch_id = $twitch_id;
    }

    /**
     * @return mixed
     */
    public function getTwitchLogin()
    {
        return $this->twitch_login;
    }

    /**
     * @param mixed $twitch_login
     */
    public function setTwitchLogin($twitch_login)
    {
        $this->twitch_login = $twitch_login;
    }

    /**
     * @return mixed
     */
    public function getTwitchDisplayName()
    {
        return $this->twitch_display_name;
    }

    /**
     * @param mixed $twitch_display_name
     */
    public function setTwitchDisplayName($twitch_display_name)
    {
        $this->twitch_display_name = $twitch_display_name;
    }

    /**
     * @return mixed
     */
    public function getTwitchBroadcasterType()
    {
        return $this->twitch_broadcaster_type;
    }

    /**
     * @param mixed $twitch_broadcaster_type
     */
    public function setTwitchBroadcasterType($twitch_broadcaster_type)
    {
        $this->twitch_broadcaster_type = $twitch_broadcaster_type;
    }

    /**
     * @return mixed
     */
    public function getLastLogin()
    {
        return $this->last_login;
    }

    /**
     * @param mixed $last_login
     */
    public function setLastLogin($last_login)
    {
        $this->last_login = $last_login;
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
}