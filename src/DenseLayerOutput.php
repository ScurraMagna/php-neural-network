<?php

class DenseLayerOutput {

  private $inputs = 0;
  private $outputs = 0;

  private $weights = [];
  private $biases = [];

  public $forward_input = [];
  public $forward_output = [];

  public $backward_input = [];
  public $backward_output = [];

  private $learning_rate = 0.1;
  private $activation = "tanh";

  public function __construct (int $inputs, int $outputs) {
    $this->inputs = $inputs;
    $this->outputs = $outputs;

    for ($i=0; $i<$outputs; $i++) {
      for ($j=0; $j<$inputs; $j++) {
        $this->weights[$i][$j] = rand() / getmaxrand() * 2 - 1;
      }
      $this->biases[$i] = rand() / getmaxrand() * 2 - 1;
    }
  }

  public function forward ($input) {
    $this->forward_input = $input;
    for ($i=0; $i<$this->outputs; $i++) {
      $sum = 0;
      for ($j=0; $j<$this->inputs; $j++) {
        $sum += $this->weights[$i][$j] * $input[$j];
      }
      $sum += $this->biases[$i];
      $this->forward_output[$i] = call_user_func_array(["Activation", $this->activation], $sum);
    }
  }

  public function backward ($target) {
    for ($j=0; $j<$this->inputs; $j++) {
      $this->backward_output[$j] = $target[$j] - $this->forward_output[$j];
    }
    $this->gradient_descent();
  }

  public function set_learning_rate(double $rate) {
    $this->learning_rate = $rate;
  }

  public set_activation_function (string $name) {
    $this->activation = $name;
  }

  private function gradient_descent () {
    for ($j=0; $j<$this->outputs; $j++) {
      $grad = $this->forward_output[$j];
      $grad = call_user_func_array(["Activation", "d".$this->activation], $grad);
      $grad *= $this->backward_input[$j] * $this->learning_rate;
      for ($i=0; $i<$this->inputs; $i++) {
        $this->weights[$i][$j] += $this->forward_input[$i] * $grad;
      }
      $this->biases[$j] += $grad;
    }
  }

  public function get_weights () {
    return $this->weights;
  }

  public function get_biases () {
    return $this->biases;
  }

  public function set_weights_and_biases($weights, $biases) {
    $this->weights = $weights;
    $this->biases = $biases;
  }

}

?>
