<?php
namespace PhpDevil\framework\components\webuser;
use PhpDevil\framework\components\Component;
use PhpDevil\framework\components\webuser\storage\SessionStorage;

class User extends Component implements UserInterface
{
    private $idenity = null;

    private $storage;

    private static $_storages = [
        'session' => SessionStorage::class,
    ];

    public function getIdenity()
    {
        return $this->idenity;
    }

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

    public function generateAuthKey()
    {
        return 'qwerty';
    }

    public function setAuthCookie($authKey)
    {
        $this->storage->save($this->idenity->getUserID(), $authKey);
    }

    public function logOut()
    {
        $this->storage->save(null, null);
    }

    public function checkAuth()
    {
        $userData = $this->storage->load();
        if (isset($userData['id'])) {
            $this->idenity = (((string) $this->config['idenity'])::findIdenity($userData['id']));
            if ($this->idenity && $this->idenity->validateAuthKey($userData['auth_key'])) {
                return true;
            }
        }
        $this->logOut();
    }

    protected function initAfterConstruct()
    {
        if (!isset($this->config['cookieStorage'])) $this->config['cookieStorage'] = 'session';

        $storageClass = static::$_storages[$this->config['cookieStorage']];
        $this->storage = new $storageClass;

        $this->checkAuth();
    }
}