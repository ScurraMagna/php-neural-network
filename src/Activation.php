<?php

class Activation {

  public function tanh ($a) {
    return tanh($a);
  }

  public function dtanh ($a) {
    return 1 - $a * $a;
  }

  public function sigmoid ($a) {
    return 1 / (1 + exp(-$a))
  }

  public function dsigmoid ($a) {
    return $a * (1 - $a);
  }

  public function ReLU ($a) {
    return $a < 0 ? 0 : $a;
  }

  public function dReLU ($a) {
    return $a < 0 ? 0 : 1;
  }

  public function SiLU ($a) {
    return $a * $this->sigmoid($a);
  }

  public function dSiLU ($a) {
    return $this->sigmoid($a) * (1 - $a) + $a;
  }
}

?>
