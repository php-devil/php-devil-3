<?php
namespace PhpDevil\framework\base\helpers\files;
use PhpDevil\framework\helpers\NamesHelper;

/**
 * Class AbstractFile
 * Загружаемый медиафайл
 * @package PhpDevil\framework\base\helpers\files
 */
abstract class AbstractFile
{
    /**
     * Параметры загрузки файла
     * (так, как описано в модели)
     * @var array
     */
    protected $config;

    /**
     * Имя файла (если он загружен и сохранен в модели)
     * @var null|string
     */
    protected $existing = null;

    /**
     * Данные из $_FILES
     * (Если в атрибут загружается новый файл)
     * @var null|array
     */
    protected $uploaded = null;

    /**
     * Новое имя файла (если файл загружен)
     * @var null
     */
    protected $_newFileName = null;

    /**
     * Первичный ключ (поле с ролью id модели). Значение добавляется в начало имени файла.
     * @var null
     */
    protected $_primaryKey = null;

    /**
     * Загрузка файла с указанием ключа
     */
    public function upload()
    {
        if ($newFile = $this->getNewFileName()){
            $origin = $destination = \Devil::getPathOf($this->config['dest']);
            $this->remove();
            if (isset($this->config['onload'])) {
                $destination .= '/origin';
            }
            if (!is_dir($destination)) mkdir ($destination, 0777, true);
            $realFileName = $destination . '/' . $this->_primaryKey . '_' . $this->getNewFileName();
            if (move_uploaded_file($this->uploaded['tmp_name'], $realFileName)) {
                if (isset($this->config['onload'])) foreach ($this->config['onload'] as $method=>$config) {
                    $this->$method($realFileName, $origin, $config, $this->_primaryKey . '_' . $this->getNewFileName());
                }
            }
        }
    }

    /**
     * Загрузка файла с указанием ключа
     */
    public function remove()
    {

        $origin = $destination = \Devil::getPathOf($this->config['dest']);
        if (isset($this->config['onload'])) {
            $destination .= '/origin';
        }
        $realFileName = $destination . '/' . $this->_primaryKey . '_' . $this->existing;
        if (file_exists($realFileName)) unlink($realFileName);

        if (isset($this->config['onload'])) foreach ($this->config['onload'] as $method=>$config) {
            $methodRemove = $method . 'Remove';
            $this->$methodRemove($origin, $this->_primaryKey . '_' . $this->existing, $config);
        }
    }

    /**
     * Установка значения первичного ключа модели
     * @param $value
     * @return $this
     */
    final public function setPrimaryKey($value)
    {
        $this->_primaryKey = $value;
        return $this;
    }

    /**
     * Получение нового имени файла с транслитом (выполняется при первом обращении)
     * @return mixed|null|string
     */
    final public function getNewFileName()
    {
        if (null === $this->_newFileName) {
            if (isset($this->uploaded['name'])) {
                $this->_newFileName = NamesHelper::transliterate($this->uploaded['name'], true);
            }
        }
        return $this->_newFileName;
    }

    /**
     * AbstractFile constructor.
     * @param $config
     * @param null $existing
     * @param null $uploaded
     */
    final public function __construct($config, $existing = null, $uploaded = null)
    {
        $this->config = $config;
        $this->existing = $existing;
        $this->uploaded = $uploaded;
    }
}