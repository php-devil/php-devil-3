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

    ];

    /**
     * Парсинг шаблона кода миграции с подстановкой реальных данных
     * @param array $variables
     * @return string
     */
    protected function parseTemplate($variables = [])
    {
        $replacements = array_merge($variables, $this->templateVariables);
        return strtr(file_get_contents(dirname(__DIR__) . '/templates/migration.tpl'), $replacements);
    }

    /**
     * Создание миграции
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
                        foreach ($models as $model) {
                            Dependencies::push($model);
                        }
                    }
                } else {
                    die ("\n\nFatal error: module " . $params[0] . " is unknown");
                }
            }

        }

        Dependencies::flush();
    }
}