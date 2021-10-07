<?php

namespace Paywant\Model;

use Paywant\JsonSerializableModel;

class Buyer extends JsonSerializableModel
{
    private $userID;
    private $userAccountName;
    private $userEmail;

    public function jsonSerialize()
    {
        return get_object_vars($this);
    }

    /**
     * Get the value of userID.
     */
    public function getUserID()
    {
        return $this->userID;
    }

    /**
     * Set the value of userID.
     *
     * @param mixed $userID
     *
     * @return self
     */
    public function setUserID($userID)
    {
        $this->userID = $userID;

        return $this;
    }

    /**
     * Get the value of userAccountName.
     */
    public function getUserAccountName()
    {
        return $this->userAccountName;
    }

    /**
     * Set the value of userAccountName.
     *
     * @param mixed $userAccountName
     *
     * @return self
     */
    public function setUserAccountName($userAccountName)
    {
        $this->userAccountName = $userAccountName;

        return $this;
    }

    /**
     * Get the value of userEmail.
     */
    public function getUserEmail()
    {
        return $this->userEmail;
    }

    /**
     * Set the value of userEmail.
     *
     * @param mixed $userEmail
     *
     * @return self
     */
    public function setUserEmail($userEmail)
    {
        $this->userEmail = $userEmail;

        return $this;
    }
}
