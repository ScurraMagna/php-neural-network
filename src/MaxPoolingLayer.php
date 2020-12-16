<?php

class MaxPoolingLayer {

  private $rows = 0;
  private $cols = 0;

  public $forward_input;
  public $forward_output;

  public $backward_input;
  public $backward_output;

  public function construct ($filter_rows, $filter_columns) {
    $this->rows = $filter_rows;
    $this->cols = $filter_columns;
  }

  public function forward ($input) {
    $this->forward_input = $input;

    $this->out_rows = count($input[0]) / $this->rows;
    $this->out_cols = count($input[0][0]) / $this->cols;

    for ($chan=0; $chan<count($input); $chan++) {
      for ($m=0; $m<$this->out_rows; $m++) {
        for ($n=0; $n<$this->out_cols; $n++) {
          $max = 0;
          for ($k=0; $k<$this->rows; $k++) {
            for ($l=0; $l<$this->cols; $l++) {
              $value = $input[$chan][$m*$this->rows+$k][$n*$this->cols+$l];
              $max = ($value > $max) ? $value : $max;
            }
          }
          $this->forward_output[$chan][$m][$n] = $max;
        }
      }
    }
  }

  public function backward ($input) {
    for ($c=0; $c<count($input); $c++) {
      $f_in = $this->forward_input[$c];
      $f_out = $this->forward_output[$c];
      for ($m=0; $m<$this->out_rows; $m++) {
        for ($n=0; $n<$this->out_cols; $n++) {
          for ($k=0; $k<$this->rows; $k++) {
            for ($l=0; $l<$this->cols; $l++) {
              $error = $f_in[$m*$this->rows+$k][$n*$this->cols+$l] == $f_out[$m][$n] ?
                       $input[$m][$n] : 0;
              $this->backward_output[$c][$m*$this->rows+$k][$n*$this->cols+$l] = $error;
            }
          }
        }
      }
    }
  }

}

?>
