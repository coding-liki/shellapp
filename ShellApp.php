<?php
namespace CodingLiki\ShellApp;

class ShellApp{
    public $params_short = [];
    public $params_long = [];
    public $required_long_params = [];
    public $short_params_string = "";
    public $long_params_array = [];
    public $params_values = [];
    public $params_array = [];

    public function __construct($params_array){
        $this->params_array = $params_array;
        $this->normalizeParams($params_array);
        $this->getInputParamsValues();
    }

    public function getParam($param_name, $default = null){
        $long_name  = $this->params_long[$param_name]  ?? null;
        $short_name = $this->params_short[$param_name] ?? null;

        return $this->params_values[$param_name] ?? $this->params_values[$long_name ?? $short_name] ?? $default;
    }

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

    public function getInputParamsValues(){
        $this->params_values = getopt($this->short_params_string, $this->long_params_array);
    }
}