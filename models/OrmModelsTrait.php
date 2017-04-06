<?php
namespace PhpDevil\framework\models;

use PhpDevil\framework\models\attributes\IntegerAttribute;
use PhpDevil\framework\models\attributes\PasswordAttribute;
use PhpDevil\framework\models\attributes\StringAttribute;
use PhpDevil\framework\models\helpers\Instantiator;
use PhpDevil\orm\generic\ConnectionInterface;

trait OrmModelsTrait
{
    /**
     * Типы атрибутов
     * @var array
     */
    protected static $extAttributeClassNames = [
        'integer'  => IntegerAttribute::class,
        'string'   => StringAttribute::class,
        'password' => PasswordAttribute::class,
    ];

    /**
     * Проверка разрешения выполнения действия на уровне строки или записи.
     * По умоляанию разрешены все действия над моделью
     * @param $action
     * @param null $item
     * @return bool
     */
    public function accessControl($action, $item = null)
    {
        return true;
    }

    /**
     * Соединение с базой данных, содержащей связанную таблицу
     * @return ConnectionInterface|null
     */
    public static function db()
    {
        $table = static::table();
        return \Devil::app()->db->getConnection($table['connection']);
    }

    public function loadFromPost($successMethod = null)
    {
        if (isset($_POST[$this->getID()])) {
            $postVars = $_POST[$this->getID()];
            $this->setAttrributesValues($postVars);
            if (null !== $successMethod) {
                if ($this->validate()) $this->$successMethod();
            }
        }
        return $this;
    }

    /**
     * Получение класса атрибута по его типу с учетом расширения типов
     * @param $type
     * @return mixed
     */
    public static function getAttributeClass($type)
    {
        if (isset(static::$extAttributeClassNames[$type])) {
            return static::$extAttributeClassNames[$type];
        } else {
            return parent::getAttributeClass($type);
        }
    }

    /**
     * Массив атрибутов модели из конфигурации
     * @return null
     */
    public static function attributes()
    {
        return Instantiator::helper()->getConfigured(static::class, 'attributes');
    }

    public static function relations()
    {
        return Instantiator::helper()->getConfigured(static::class, 'relations');
    }

    public static function rules()
    {
        return Instantiator::helper()->getConfigured(static::class, 'rules');
    }

    public static function labels()
    {
        return Instantiator::helper()->getConfigured(static::class, 'labels');
    }

    public static function setRoleField($role, $column)
    {
        Instantiator::helper()->setRoleName(static::class, $role, $column);
    }

    public static function roles()
    {
        return Instantiator::helper()->getConfigured(static::class, 'roles');
    }

    /**
     * Параметры связанной с моделью таблицы
     * @return null
     */
    public static function table()
    {
        if ($table = Instantiator::helper()->getConfigured(static::class, 'table')) {
            if (!isset($table['connection'])) $table['connection'] = 'main';
            return $table;
        } else {
            return null;
        }
    }

    /**
     * Создание инстанса модели через хелпер
     * @return mixed
     */
    public static function model()
    {
        return Instantiator::helper()->load(static::class);
    }
}