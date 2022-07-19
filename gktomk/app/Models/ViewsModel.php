<?php


namespace GKTOMK\Models;

use GKTOMK\Config;

class ViewsModel
{
    /**
     * @var array
     */
    private static $DATATPL = [];
    private static $OUTPUT;

    /*
     * Универсальна функция
     * Создает либо выводит переменную шаблона
     * */
    public function varTpl($name_var, $data_var = false)
    {
        if ((empty($data_var) or $data_var == false) and !is_array($name_var)) {
            return self::getVar($name_var);
        }
        // Если получили массив переменных, тогда сохраняем переменные
        if (is_array($name_var)) {
            self::setVars($name_var);
            return true;
        }
        return self::setVar($name_var, $data_var);
    }

    /*
     * Создает переменные принимая массив данных
     * */

    public function getVar($name_var)
    {
        return !empty(self::$DATATPL[$name_var]) ? self::$DATATPL[$name_var] : null;
    }

    /*
     * Указывает значение для переменной
     * */

    public function setVars($data)
    {
        foreach ($data as $name_var => $data_var) {
            self::setVar($name_var, $data_var);
        }
    }

    /*
     * Добавляет значение к переменной (полезно при добавлении нового контента)
     * */

    public function setVar($name_var, $data_var)
    {
        self::$DATATPL[$name_var] = $data_var;
        return self::$DATATPL[$name_var];
    }

    /*
     * Метод отдает значение переменной шаблона
     * */

    public function parseTpl($tpl, $output = true)
    {
        self::__construct();
        $parse = self::parse(self::$DATATPL, $tpl . '.tpl');
        if ($output == false) {
            self::addToVar('CONTENT', $parse);
            return $this;
        }
        self::$OUTPUT = $parse;
        return $this;
    }

    /*
     * Функция добавляет указанный шаблон в контент
     * если $output = false, метод сохранит шаблон в переменную content
     * */

    public function __construct()
    {
        $this->tpl_root_dir = __DIR__ . '/../../templates/default/';
        Config::init();
        // Указываем стандартные глобальные переменные для шаблона
        // self::varTpl(['URL_SITE', 'TPL_NAME'], [CONFIG['url_site'], CONFIG['tpl_name']]);
        self::setVars([
            'EOL' => PHP_EOL,
            'URL_SITE' => CONFIG['url_site'],
            'TPL_NAME' => CONFIG['tpl_name']
        ]);
    }

    public function parse(
        $data,
        $template_path
        //$templates_root_dir = FALSE,
        //$no_global_vars = FALSE
        // $profiling = FALSE - пока убрали
    )
    {

        $W = new Websun(array(
            'data' => $data,
            'templates_root' => $this->tpl_root_dir,
            'no_global_vars' => 'false',
        ));
        $tpl = $W->get_template($template_path);
        $W->current_template_filepath = $template_path;
        $W->templates_current_dir = pathinfo($W->template_real_path($template_path), PATHINFO_DIRNAME) . '/';
        $string = $W->parse_template($tpl);
        return $string;
    }

    public function addToVar($name_var, $data_var)
    {
        $new_data_var = self::getVar($name_var) . $data_var;
        self::setVar($name_var, $new_data_var);
        return true;
    }

    /*
     * Выводит созданный шаблон + заголовки
     * */

    public function output()
    {
        echo self::$OUTPUT;
    }

    /**
     * Регистрирует новую функцию для шаблонизатора
     * */
    public function regFunc($func)
    {
        global $WEBSUN_ALLOWED_CALLBACKS;
        $WEBSUN_ALLOWED_CALLBACKS[] = $func;
        return $this;
    }

    /**
     * Дебаг
     */
    public function debug()
    {

        var_dump(self::$DATATPL);
        global $WEBSUN_ALLOWED_CALLBACKS;
        var_dump($WEBSUN_ALLOWED_CALLBACKS);
    }

}