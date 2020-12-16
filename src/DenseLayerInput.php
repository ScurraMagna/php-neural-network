<?php

class DenseLayerInput {

  private $input_map = 0;

  public $forward_input = [];
  public $forward_output = [];

  public $backward_input = [];
  public $backward_output = [];

  public function __construct () {}

  public function forward ($input) {
    $input = !is_array($input) ? func_get_args() : $input;
    $this->forward_input = $input;
    $this->map($input);
    $this->forward_output = $this->flatten($input);
  }

  public function backward ($input) {
    $this->backward_input = $input;
    $this->backward_output = $this->reshape($input);
  }

  private function flatten (array $array) {
    $result = [];
    foreach ($array as $item) {
      if (is_array($item)) {
        $result = array_merge($result, $this->flatten($item));
      }
      else {
        $result[] = $item;
      }
    }
    return $result;
  }

  private function map (array $array) {
    $this->input_map[] = count($array);
    if (is_array($array[0])) {$this->map($array[0]);}
  }

  private function reshape (array $input) {
    $result = $input;
    for ($i=0; $i<count($this->input_map); $i++) {
      $array = [];
      $tmp = [];
      foreach($input as $value) {
        $tmp[] = $value;
        if (count($tmp) == $this->input_map[$i]-1) {
          $array[] = $tmp;
          $tmp = [];
        }
      }
      $result = $array;
    }
    return $result;
  }

}

?>
