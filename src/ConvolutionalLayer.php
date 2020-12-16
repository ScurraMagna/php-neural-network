<?php

class ConvolutionalLayer {

  private $weights = [];
  private $rows = 0;
  private $cols = 0;
  private $biases = [];

  private $inputs = 0;
  private $outputs = 0
  private $padding = 0;
  private $stride = 0;

  private $in_rows = 0;
  private $in_cols = 0;
  private $out_rows = 0;
  private $out_cols = 0;

  public $forward_input = [];
  public $forward_output = [];

  public $backward_input = [];
  public $backward_output = [];

  private $learning_rate = 0;

  public function __construct ($inputs, $outputs, $rows, $cols, $padding=0, $stride=1) {
    $this->inputs = $inputs; //number of input channels
    $this->outputs = $outputs; //number of filters

    $this->rows = $rows; //height of filters
    $this->cols = $cols; //width of filters

    $this->padding = $padding;
    $this->stride = $stride;

    for ($i=0; $i<$outputs; $i++) {
      for ($j=0; $j<$inputs; $j++) {
        for ($k=0; $k<$rows; $k++) {
          for ($l=0; $l<$cols; $l++) {
            $this->weights[$i][$j][$k][$l] = rand() / getmaxrand() * 2 - 1;
          }
        }
      }
      $this->biases[$i] = rand() / getmaxrand() * 2 - 1;
    }
  }

  public function forward ($input) {
    $this->forward_input = $input;

    $this->in_rows = count($input[0]);
    $this->in_cols = count($input[0][0]);
    $this->out_rows = ($this->in_rows-$this->rows+2*$this->padding)/$this->strides+1;
    $this->out_cols = ($this->in_cols-$this->cols+2*$this->padding)/$this->strides+1;

    for ($i=0; $i<$this->outputs; $i++) { //foreach output channel
      for ($m=0; $m<$this->out_rows; $m++) {   //foreach output cells
        for ($n=0; $n<$this->out_cols; $n++) { //from given output channel
          $sum = 0;
          for ($k=0; $k<$this->rows; $k++) {   //foreach weights
            for ($l=0; $l<$this->cols; $l++) { //cells
              $x = $m * $this->strides - $this->padding + $k; //sum of product of 
              $y = $n * $this->strides - $this->padding + $l; //weights with each input
              for ($j=0; $j<$this->inputs; $j++) {            //channels
                $sum += ($x>0 && $x<$this->in_rows && $y>0 && $y<$this->in_cols) ?
                        $this->weights[$i][$j][$k][$l] * $input[$j][$x][$y] : 0; //
              }
            }
          }
          $sum += $this->biases[$i];
          $sum = call_user_func_array(["Activation", $this->activation], $sum);
          $this->forward_output[$i][$m][$n] = $sum;
        }
      }
    }
  }

  public function backward ($input, $last=false) {
    $this->backward_input = $input;
    for ($j=0; $j<$this->inputs; $j++) {
      for ($m=0; $m<$this->out_rows; $m++) {
        for ($n=0; $n<$this->out_cols; $n++) {
          for ($k=0; $k<$this->rows; $k++) {
            for ($l=0; $l<$this->cols; $l++) {
              $x = $m * $this->strides - $this->padding + $k;
              $y = $n * $this->strides - $this->padding + $l;
              for ($i=0; $i<$this->outputs; $i++) {
                if ($x>0 && $x<$this->in_rows && $y>0 && $y<$this->in_cols) {
                  $this->backward_output[$j][$x][$y] += $this->weight[$i][$j][$k][$l] * $input[$i][$m][$n];
                }
              }
            }
          }
        }
      }
    }
  }

  private function gradient_descent () {
    for ($i=0; $i<$this->outputs; $i++) {
      for ($m=0; $m<$this->out_rows; $m++) {
        for ($n=0; $n<$this->out_cols; $n++) {
          $grad = $this->forward_output[$i][$m][$n];
          $grad = call_user_func_array(["Activation", "d".$this->activation], $grad);
          $grad *= $this->backward_input[$i][$m][$n] * $this->learning_rate;
          for ($j=0; $j<$this->inputs; $j++) {
            for ($k=0; $k<$this->rows; $k++) {
              for ($l=0; $l<$this->cols; $l++) {
                $x = $m * $this->strides - $this->padding + $k;
                $y = $n * $this->strides - $this->padding + $l;
                $this->weights[$i][$j][$k][$l] += ($x>0 && $x<$this->in_rows && $y>0 && $y<$this->in_cols) ?
                                                  $this->forward_input[$j][$x][$y] * $grad : 0;
              }
            }
          }
          $this->biases[$i] += $grad;
        }
      }
    }
  }

  public function set_learning_rate(double $rate = 0.1) {
    $this->learning_rate = $rate;
  }

  public set_activation_function (string $name) {
    $this->activation = $name;
  }
  
}

?>
