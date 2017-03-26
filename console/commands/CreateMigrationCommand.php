<?php
namespace PhpDevil\framework\console\commands;

class CreateMigrationCommand extends AbstractConsoleCommand
{
    public function execute($params = null)
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
                    '${classname}' => 'm_' . $time,
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