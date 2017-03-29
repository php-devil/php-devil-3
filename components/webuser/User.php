<?php
namespace PhpDevil\framework\components\webuser;
use PhpDevil\framework\components\Component;

class User extends Component implements UserInterface
{
    private $idenity = null;

    public function login($username, $password)
    {
        if ($idenity = (((string) $this->config['idenity'])::findByUserName($username))) {
            if ($idenity->validatePassword($password)) {
                $this->idenity = $idenity;
                $authKey = $this->generateAuthKey();
                $this->idenity->setAuthKey($authKey);
                $this->setAuthCookie($authKey);
                return true;
            }
        } else {
            $this->logOut();
        }
        return false;
    }

    public function logOut()
    {

    }

    protected function initAfterConstruct()
    {


    }
}