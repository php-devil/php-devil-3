<?php
namespace PhpDevil\framework\modules\migration\console;
use PhpDevil\framework\console\commands\AbstractConsoleCommand;
use PhpDevil\framework\modules\migration\components\Dependencies;

/**
 * Class CreateCommand
 *
 * Создание пустой миграции для указания списка изменений вручную или
 * миграции на основе конфигурации модели одного из модулей.
 * Для создания миграции по модели в командной строке используются теги модуля и модели.
 * Тегом приложения всегда является тег app
 *
 * @package PhpDevil\framework\modules\migration\console
 */
class CreateCommand extends AbstractConsoleCommand
{
    /**
     * Дефолтные замены шаблона миграции на пустые занчения
     * (чтобы можно было передавать только параметры, содержащие значения)
     * @var array
     */
    protected $templateVariables = [
        //todo: define default values
    ];

    /**
     * Парсинг шаблона кода миграции с подстановкой реальных данных
     * @param array $variables
     * @param $template
     * @return string
     */
    protected function parseTemplate($variables = [], $template = null)
    {
        if (null === $template) $template = 'migration';
        $replacements = array_merge($variables, $this->templateVariables);
        return strtr(file_get_contents(dirname(__DIR__) . '/templates/' . $template . '.tpl'), $replacements);
    }

    /**
     * Создание миграции для модуля
     * @param null $params
     */
    public function execute($params = null)
    {
        echo "\nPHPDevil create-migration tool";
        if (!empty($params[0])) {
            if ('app' === $params[0]) {
                $module = \Devil::app();
            } else {
                if ($module = \Devil::app()->loadModule($params[0])) {
                    $models = $module::models();
                    if (isset($params[1])) {
                        if (isset($models[$params[1]])) {
                            Dependencies::push($models[$params[1]]);
                        } else {
                            die ("\n\nFatal error: module " . $params[0] . " has not model " . $params[1]);
                        }
                    } else {
                        foreach ($models as $n=>$model) {
                            echo "\n\t-model $n -class $model";
                            Dependencies::push($model);
                        }
                    }
                } else {
                    die ("\n\nFatal error: module " . $params[0] . " is unknown");
                }
            }

        }
        $real = Dependencies::flush();
        foreach ($real as $connection=>$commands) {
            $this->save($connection, $commands);
        }
    }

    /**
     * Сохранение миграции
     * @param $connection
     * @param $commands
     */
    protected function save($connection, $commands)
    {
        $path = \Devil::getPathOf('@app/migrations/' . $connection);
        if (!file_exists($path . '/m_0.php')) $this->createMigrationTable($connection);
        $replace['${time}'] = $time = date('YmdHis');
        $replace['${connection}'] = $connection;
        $replace['${classname}'] = $class = 'm_' . $time;
        $replace['${up_body}'] = implode("\n", $commands['up']);
        $replace['${down_body}'] = implode("\n", array_reverse($commands['down']));
        file_put_contents($path . '/' . $class . '.php', $this->parseTemplate($replace));
    }

    /**
     * Нулевая минграция - создание/удаление таблицы логирования выполненных миграций
     * @param $connection
     */
    protected function createMigrationTable($connection)
    {
        $path = \Devil::getPathOf('@app/migrations/' . $connection);
        if (!is_dir($path)) mkdir($path, 0777, true);
        file_put_contents($path . '/m_0.php', $this->parseTemplate(['${connection}'=>$connection], 'migrations_table'));
    }
}