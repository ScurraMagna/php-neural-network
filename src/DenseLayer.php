<?php

class DenseLayer {

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
      $sum = call_user_func_array(["Activation", $this->activation], $sum)
      $this->forward_output[$i] = $sum;
    }
  }

  public function backward ($input) {
    $this->backward_input = $input;
    for ($j=0; $j<$this->inputs; $j++) {
      $sum = 0;
      for ($i=0; $i<$this->outputs; $i++) {
        $sum += $this->weights[$i][$j] * $input[$i];
      }
      $this->backward_output[$j] = $sum;
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
    for ($i=0; $i<$this->outputs; $i++) {
      $grad = $this->forward_output[$i];
      $grad = call_user_func_array(["Activation", "d".$this->activation], $grad);
      $grad *= $this->backward_input[$i] * $this->learning_rate;
      for ($j=0; $j<$this->inputs; $j++) {
        $this->weights[$i][$j] += $this->forward_input[$j] * $grad;
      }
      $this->biases[$i] += $grad;
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
