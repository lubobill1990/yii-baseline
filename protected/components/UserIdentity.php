<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity
{
    private $user_id;
    private $user_activated;

    public function getId()
    {
        return $this->user_id;
    }

    public function getUserActivated()
    {
        return $this->user_activated;
    }

    /**
     * Authenticates a user.
     * The example implementation makes sure if the username and password
     * are both 'demo'.
     * In practical applications, this should be changed to authenticate
     * against some persistent user identity storage (e.g. database).
     * @return boolean whether authentication succeeds.
     */
    public function authenticate()
    {
        $user = User::model()->find('email=:email or username=:username', array(':username' => $this->username, ':email' => $this->username));

        if ($user === NULL) {
            $this->errorCode = self::ERROR_USERNAME_INVALID;
        } else {
            $this->user_id = $user->id;
            $this->username = $user->username;
            $this->user_activated = $user->has_been_activated == 'yes';
            if ($user->authorizePassword($this->password)) {
                $this->errorCode = self::ERROR_NONE;
            } else {
                $this->errorCode = self::ERROR_PASSWORD_INVALID;
            }
        }
        return !$this->errorCode;
    }

}
