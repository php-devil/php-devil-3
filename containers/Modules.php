<?php
namespace PhpDevil\franework\containers;

use PhpDevil\framework\base\ModuleInterface;
use PhpDevil\framework\components\InvalidInterfaceException;
use PhpDevil\framework\containers\modules\ModuleNotFoundException;
use PhpDevil\framework\models\ModelInterface;

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
            if ($className instanceof ModuleInterface) {
                if (isset($config['mount'])) {
                    $this->urls[$className] = $config['mount'];
                }
                $this->known[$className] = [
                    'tagName' => $tag,
                    'config'  => $config,
                ];
                $this->tags[$tag] = $className;

            } else {
                throw new InvalidInterfaceException(['module', $className, ModelInterface::class]);
            }
        } else {
            throw new ModuleNotFoundException($tag . ': ' . $className);
        }
    }

    /**
     * При аервом вызове контейнер зарегистрирует себе все модули
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