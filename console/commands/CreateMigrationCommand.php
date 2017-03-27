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
        file_put_contents(\Devil::getPathOf('@app/migrations') . '/' . $conn . '/' . $fileName, $template);
        echo "\n\n\nMigration created " . \Devil::getPathOf('@app/migrations') . '/' . $conn . '/' . $fileName;
    }

    private function addModelMigration($class, &$up, &$down)
    {
        if ($table = $class::table()) {
            if ('main' == $table['connection']) {
                echo "\n\npreparing migrations for {$class}\n...";
                $up   .= "\n\t\t" . '$this->createTable(\'' . $table['connection'] . '\', \'' . $table['name'] . '\', [';
                $attributes = $class::attributes();
                foreach ($attributes as $k=>$v) {
                    $up .= "\n\t\t\t\t'{$k}' => ['{$v['type']}', ";
                    if (is_array($v['flags'])) {
                        $up .= "['" .implode("', '", array_values($v['flags'])). "'], ";
                    } else {
                        $up .= "[], ";
                    }
                    if (isset($v['default'])) $up .= "'{$v['default']}'"; else $up .= 'null';
                    $up .= '],';
                }
                $up   .= "\n\t\t\t" . '], [';
                $up   .= "\n\t\t\t" . ']);';

                $down .= "\n\t\t" . '$this->dropTable(\'' . $table['connection'] . '\', \'' . $table['name'] . '\');';
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