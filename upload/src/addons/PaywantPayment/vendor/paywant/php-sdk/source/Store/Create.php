<?php

namespace Paywant\Store;

use Paywant\Config;
use Paywant\Model;
use Paywant\Model\Buyer;
use Paywant\Request;
use Paywant\Utilities\Helper;

class Create extends Request
{
    const PATH = '/store/token';

    private Buyer $buyer;

    private $userAccountName;
    private $userEmail;
    private $userID;
    private $userIPAddress;
    private $redirect_url;
    private $fail_redirect_url;
    private $callback_url;

    public function __construct(Config $config)
    {
        parent::__construct($config, self::PATH);
        $this->buyer = new Model\Buyer();
        $this->userIPAddress = Helper::getIPAddress();

        $this->language = 'tr';
        $this->redirect_url = '';
        $this->fail_redirect_url = '';
        $this->callback_url = '';
    }

    public function jsonSerialize()
    {
        return get_object_vars($this);
    }

    public function execute()
    {
        return parent::post($this->jsonSerialize());
    }

    public function getResponse($object = false)
    {
        return parent::getResponse($object);
    }

    public function getError($object = false)
    {
        return parent::getError($object);
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
     * Get the value of userIPAddress.
     */
    public function getUserIPAddress()
    {
        return $this->userIPAddress;
    }

    /**
     * Set the value of userIPAddress.
     *
     * @param mixed $userIPAddress
     *
     * @return self
     */
    public function setUserIPAddress($userIPAddress)
    {
        $this->userIPAddress = $userIPAddress;

        return $this;
    }

    /**
     * Get the value of redirect_url.
     */
    public function getRedirect_url()
    {
        return $this->redirect_url;
    }

    /**
     * Set the value of redirect_url.
     *
     * @param mixed $redirect_url
     *
     * @return self
     */
    public function setRedirect_url($redirect_url)
    {
        $this->redirect_url = $redirect_url;

        return $this;
    }

    /**
     * Get the value of fail_redirect_url.
     */
    public function getFail_redirect_url()
    {
        return $this->fail_redirect_url;
    }

    /**
     * Set the value of fail_redirect_url.
     *
     * @param mixed $fail_redirect_url
     *
     * @return self
     */
    public function setFail_redirect_url($fail_redirect_url)
    {
        $this->fail_redirect_url = $fail_redirect_url;

        return $this;
    }

    /**
     * Get the value of callback_url.
     */
    public function getCallback_url()
    {
        return $this->callback_url;
    }

    /**
     * Set the value of callback_url.
     *
     * @param mixed $callback_url
     *
     * @return self
     */
    public function setCallback_url($callback_url)
    {
        $this->callback_url = $callback_url;

        return $this;
    }

    /**
     * Get the value of buyer.
     */
    public function getBuyer()
    {
        return $this->buyer;
    }

    /**
     * Set the value of buyer.
     *
     * @return self
     */
    public function setBuyer(Buyer $buyer)
    {
        $this->buyer = $buyer;

        $this->setUserAccountName($buyer->getUserAccountName());
        $this->setUserEmail($buyer->getUserEmail());
        $this->setUserID($buyer->getUserID());

        return $this;
    }
}
