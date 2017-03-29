<?php
namespace PhpDevil\framework\containers;

use PhpDevil\framework\base\ModuleInterface;
use PhpDevil\framework\components\InvalidInterfaceException;
use PhpDevil\framework\containers\modules\ModuleNotFoundException;
use PhpDevil\framework\containers\modules\UnknownTagException;

/**
 * Class Modules
 * Менеджер модулей.
 * @package PhpDevil\franework\containers
 */
class Modules extends AbstractContainer
{
    private static $instance = null;

    private $tags = [];

    private $known = [];

    private $modules = [];

    private $urls = [];

    private $requests = [];

    private $isResortNeeded = false;

    public function load($className)
    {
        $tag = $className;
        if (isset($this->tags[$className])) $className = $this->tags[$className];
        if (!isset($this->modules[$className])) {
            if (isset($this->known[$className])) {
                $module = new $className($this->known[$className]['config']);
                $this->modules[$className] = $module;
            } else {
                throw new UnknownTagException($tag);
            }
        }
        return $this->modules[$className];
    }

    public function getAll()
    {
        return $this->known;
    }

    /**
     * Поиск тега модуля по вхождению в начало переданного адреса
     * @param $request
     * @return mixed|null
     */
    public function getTagByUrl($request)
    {
        $this->resort();
        foreach ($this->requests as $url=>$tag) {
            if (0 === strrpos($request, $url)) return $tag;
        }
        return null;
    }

    /**
     * Получение фронтенд-адреса по тегу
     * @param $tagName
     * @return mixed|null
     */
    public function getUrlByTag($tagName)
    {
        if (isset($this->urls[$tagName])) {
            return $this->urls[$tagName];
        } else {
            return null;
        }
    }

    /**
     * Получение фронтенд-адреса по имени класса
     * @param mixed $className
     * @return mixed|null
     */
    public function getUrlByClassName($className)
    {
        if (is_object($className)) $className = get_class($className);
        if (isset($this->known[$className]['config']['mount'])) {
            return $this->known[$className]['config']['mount'];
        } else {
            return null;
        }
    }

    /**
     * Получение тега по имени класса
     * @param mixed $className
     * @return mixed|null
     */
    public function getTagByClassName($className)
    {
        if (is_object($className)) $className = get_class($className);
        if (isset($this->known[$className]['tagName'])) {
            return $this->known[$className]['tagName'];
        } else {
            return null;
        }
    }

    /**
     * Получение корневой директории по имени класса
     * @param mixed $className
     * @return mixed|null
     */
    public function getLocationByClassName($className)
    {
        if (is_object($className)) $className = get_class($className);
        if (isset($this->known[$className]['location'])) {
            return $this->known[$className]['location'];
        } else {
            return null;
        }
    }

    /**
     * Пересортировка запросов модулей перед поиском по URL
     * адресу. Выполняется автоматои перед запросом при необходимости
     */
    protected function resort()
    {
        if ($this->isResortNeeded) {
            krsort($this->requests);
            $this->isResortNeeded = false;
        }
    }

    /**
     * Установка адреса монтирования модулю для
     * динамического подключения модулей к приложению
     * @param $tagName
     * @param $url
     */
    public function mount($tagName, $url)
    {
        if (isset($this->tags[$tagName])) {
            $this->urls[$tagName] = $url;
            $this->requests[$url] = $tagName;
            $this->isResortNeeded = true;
        }
    }

    /**
     * Регистратор модулей
     * @param $tag
     * @param $config
     * @throws InvalidInterfaceException
     * @throws ModuleNotFoundException
     */
    public function register($tag, $config)
    {
        $className = $config['class'];
        unset($config['class']);
        if (class_exists($className)) {
            $this->tags[$tag] = $className;
            if (isset($config['mount'])) {
                $this->mount($tag, $config['mount']);
            }
            $this->known[$className] = [
                'tagName' => $tag,
                'config'  => $config,
                'location' => dirname((new \ReflectionClass($className))->getFileName())
            ];
        } else {
            throw new ModuleNotFoundException($tag . ': ' . $className);
        }
    }

    /**
     * ПРоверка наличия в контейнере зарегистрированных модулей
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->known);
    }

    /**
     * При первом вызове контейнер зарегистрирует себе все модули
     * из конфигурационного файла приложения
     * Modules constructor.
     */
    private function __construct()
    {
        if ($modules = \Devil::app()->getConfig('modules')) foreach ($modules as $tag=>$config) {
            $this->register($tag, $config);
        }
    }

    public static function container()
    {
        if (null === self::$instance) self::$instance = new self;
        return self::$instance;
    }
}