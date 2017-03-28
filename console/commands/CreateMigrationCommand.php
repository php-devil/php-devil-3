<?php
namespace PhpDevil\framework\console\commands;

class CreateMigrationCommand extends AbstractConsoleCommand
{
    public function execute($params = null)
    {
        $argument = array_shift($params);
        switch ($argument) {
            case '-app':
                return $this->createModelMigration($params, null);
                break;

            case '--module':
                $module = array_shift($params);
                return $this->createModuleMigration($module, $params);
                break;

            default:
                return $this->createEmptyMigration($argument);
        }
    }

    private function createModuleMigration($module, $params = [])
    {
        $up_sql   = '';
        $down_sql = "";
        echo "\nModule migration create tool";
        if ($moduleClass = \Devil::app()->loadModule($module)) {
            echo " for {$module}";
            $moduleModels = $moduleClass::models();
            if (isset($params[0])) {
                if (isset($moduleModels[$params[0]])) {
                    $this->addModelMigration($moduleModels[$params[0]], $up_sql, $down_sql);
                } else {
                    echo "\nError: model {$params[0]} not found in module {$module}";
                }
            } else {
                echo "\nCreating migration for any configured models";
            }
        } else {
            echo "\nError: module {$module} not found";
        }

        if (!empty($up_sql) && !empty($down_sql)) {
            $time = time();
            echo "\n\nWriting migration file";
            $replace = [
                '${time}' => $time,
                '${connection}' => 'main',
                '${classname}'  => 'm_' . $time,
                '${up_body}'  => $up_sql,
                '${down_body}'  => $down_sql,
            ];
            $this->fromTemplate($replace);
        }
    }

    private function fromTemplate($arr)
    {
        $time = $arr['${time}'];
        $conn = $arr['${connection}'];
        $fileName = $arr['${classname}'] . '.php';
        $template = strtr(file_get_contents(\Devil::getPathOf('@devil/codegen/templates/migration.tpl')), $arr);

        $fullFileName = \Devil::getPathOf('@app/migrations') . '/' . $conn . '/' . $fileName;
        if (!is_dir(dirname($fullFileName))) {
            mkdir(dirname($fullFileName), 0777, true);
        }
        file_put_contents($fullFileName, $template);
        echo "\n\n\nMigration created " . \Devil::getPathOf('@app/migrations') . '/' . $conn . '/' . $fileName;
    }



    private function _buildKeyString($name, $config)
    {
        $query = "->key('{$name}')";

        if (isset($config['type'])) {
            $query .= "withType('{$configType}')";
            unset($config['type']);
        } else {
            $type = 'default';
        }

        if ('foreign' == $type) {
            echo "\nIMPLEMENT FK DEFINITION";

        } else {
            if (isset($config['columns'])) {
                $columns = $config['columns'];
                unset($config['columns']);
            } else {
                $columns = $config;
            }
            if (is_array($columns)) {
                $query .= "->onColls(['" . implode("', '", $columns) . "'])";
            } else {
                $query .= "->onColls('{$columns}')";
            }
        }
        return $query;
    }


    /**
     * Построение строки атрибута
     * @param $name
     * @param $config
     */
    private function _buildAttributeString($name, $config)
    {
        $query = "->column('{$name}', '{$config['type']}')";
        if (!isset($config['nullable']) || $config['nullable']) $query .= ''; else $query .= '->notNull()';
        if (isset($config['default'])) $query .= "->defaultValue('{$config['default']}'')";
        if (isset($config['extra'])) $query .= "->extra('{$config['extra']}')";
        return $query;
    }

    private function addModelMigration($class, &$up, &$down)
    {
        if ($table = $class::table()) {
            if ('main' == $table['connection'] && isset($table['name'])) {
                echo "\n\npreparing migrations for {$class}\n...";
                $down .= "\n\t\t\\Devil::app()->db->getSchema('main')->getTable('{$table['name']}')->drop();";
                $up   .= "\n\t\t\\Devil::app()->db->getSchema('main')->getTable('{$table['name']}')->create()";

                $attributes = $class::attributes();
                if (!empty($attributes)) foreach ($attributes as $k=>$v) {
                    $up .= "\n\t\t\t" . $this->_buildAttributeString($k, $v);
                }

                if (isset($table['keys']) && !empty($table['keys'])) foreach ($table['keys'] as $k=>$v) {
                    $up .= "\n\t\t\t" . $this->_buildKeyString($k, $v);
                }


                $up .= "\n\t\t\t->save();";
                echo "done";
            } else {
                echo "\n\nWarning: automatic migrations are allowed only for connection main";
            }
        } else {
            echo "\nError: class {$class} cannot be migrated to database";
        }
    }

    private function old__createEmptyMigration($params = null)
    {
        echo "\n\nPHPDevil 3 Migration creation tool";
        if (isset($params[0])) {
            echo "\nCreate custom migration for database {$params[0]}? [Y/N]?\n>";
            $line = fgetc(STDIN);
            if ('y' == $line) {
                $time = time();
                $replace = [
                    '${time}' => $time,
                    '${connection}' => $params[0],
                    '${classname}'  => 'm_' . $time,
                ];
                $fileName = 'm_' . $time . '.php';
                if (!is_dir(\Devil::getPathOf('@app/migrations') . '/' . $params[0])) {
                    mkdir(\Devil::getPathOf('@app/migrations') . '/' . $params[0], 0777, true);
                }
                $template = strtr(file_get_contents(\Devil::getPathOf('@devil/codegen/templates/migration.tpl')), $replace);
                file_put_contents(\Devil::getPathOf('@app/migrations') . '/' . $params[0] . '/' . $fileName, $template);
                echo "\nMigration created " . \Devil::getPathOf('@app/migrations') . '/' . $params[0] . '/' . $fileName;
            } else {
                echo "\nMigration creation cancelled";
            }
        } else {
            echo "\n error: database connection is undefined.";
        }
    }
}