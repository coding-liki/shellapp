<?php
namespace CodingLiki\ShellApp;

/**
 * Класс разбирает параметры командной строки как консольное приложение
 * В конструктор можно передать массив вида
 * [
 *      "f" => [
 *         "first" 
 *      ],
 *      "s" => [
 *          "second",
 *          "required" => true
 *      ]
 * ]
 */
class ShellApp{
    public $params_short = [];
    public $params_long = [];
    public $required_long_params = [];
    public $short_params_string = "";
    public $long_params_array = [];
    public $params_values = [];
    public $params_array = [];

    /**
     * Конструктор
     *
     * @param array $params_array массив названий параметров
     */
    public function __construct(array $params_array){
        $this->params_array = $params_array;
        $this->normalizeParams($params_array);
        $this->getInputParamsValues();
    }

    /**
     * Возвращает значение параметра по имени 
     *
     * @param string $param_name имя параметра короткое либо длинное
     * @param mixed $default значение возвращается, если не найдено ни для короткое ни для длинного параметра
     * @return mixed
     */
    public function getParam(string $param_name, $default = null){
        $long_name  = $this->params_long[$param_name]  ?? null;
        $short_name = $this->params_short[$param_name] ?? null;

        return $this->params_values[$param_name] ?? $this->params_values[$long_name ?? $short_name] ?? $default;
    }

    /**
     * Проверяет наличие значений для обязательных параметров
     *
     * @return bool
     */
    public function checkRequiredParams(){
        foreach($this->required_long_params as $long_param){
            $short_param = $this->params_short[$long_param];
            $has_value = isset($this->params_values[$short_param]) || isset($this->params_values[$long_param]);

            if(!$has_value){
                return false;
            }
        }

        return true;
    }

    /**
     * Нормализует массив названий параметров в строку коротких и массив длиннных
     * для использования с getopt()
     *
     * @param array $params массив названий параметров
     * @return void
     */
    public function normalizeParams(array $params){
        foreach($params as $param_short => $param){
            $short_version = $param_short.":";

            $param_long   = $param['long'] ?? $param[0];
            $long_version = $param_long.":";

            $required = $param['required'] ?? $param[1] ?? false;
            if(!$required){
                $short_version .= ":";
                $long_version  .= ":";
            } else {
                $this->required_long_params[] = $param_long;
            }

            $this->params_short[$param_long] = $param_short;
            $this->params_long[$param_short] = $param_long;

            $this->short_params_string .= $short_version;
            $this->long_params_array[] = $long_version;
        }
    }

    /**
     * Парсит значения параметров с помощью getopt
     *
     * @return void
     */
    public function getInputParamsValues(){
        $this->params_values = getopt($this->short_params_string, $this->long_params_array);
    }
}