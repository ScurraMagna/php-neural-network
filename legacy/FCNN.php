<?php
include("Matrix.php");

class FCNN {

  private $nodes;
  private $layers;

  private $learning_rate;

  private $weights;
  private $biases;

  private $data = array(
    'training' => array(),
    'controle' => array()
  );

  private $calculate = array();
  private $errors = array();

  public function __construct ($nodes) {
    $nodes = !is_array($nodes) ? func_get_args() : $nodes;

    $this->nodes = $nodes;
    $this->layers = count($nodes);

    $this->set_weights_and_biases();
  }

  public function set_data_training($data) {
    $this->data->training->input = array();
    $this->data->training->output = array();
    for ($i=0; $i<count($data); $i++) {
      $this->data->training->input[$i] = data[$i]["input"];
      $this->data->training->output[$i] = data[$i]["output"];
    }
  }

  public function set_data_controle($data) {
    $this->data->controle->input = array();
    $this->data->controle->output = array();
    for ($i=0; $i<count($data); $i++) {
      $this->data->controle->input[$i] = data[$i]["input"];
      $this->data->controle->output[$i] = data[$i]["output"];
    }
  }

  public function forward ($input) {
    $this->calculate[0] = Matrix::from_array($input);
    for ($layer=1; $layer<$this->layers; $layer++) {
      $last = $this->calculate[$layer-1];
      $weight = $this->weights[$layer-1];
      $this->calculate[$layer] = Matrix::mult($weight, $last);
      $this->calculate[$layer]->add($this->biases[$layer-1]);
      $this->calculate[$layer]->map(function ($v, $i, $j) {
        return tanh($v);
      });
    }
    return $this->calculate[$this->layers-1]->to_array();
  }

  public function backward ($target) {
    $expected = Matrix::from_array($target);
    $answer = $this->calculate[$this->layers-1];
    $this->errors[$this->layers-1] = Matrix::sub($expected, $answer);
    $this->update_weights_and_biases($this->layers-1);
    for ($layer=$this->layers-2; $layer>=0; $layer--) {
      $error = $this->errors[$layer+1];
      $transpose = Matrix::transpose($this->weights[$layer]);
      $this->errors[$layer] = Matrix::mult($transpose, $error);
      $this->update_weights_and_biases($layer+1);
    }
  }

  public function train ($epoch) {
    for ($i=0; $i<$epoch; $i++) {
      $rand = (rand() / getrandmax());
      $rand_index = round($rand * (count($this->data->training->input)-1));

      $this->forward($this->data->training->input[$rand_index]);
      $this->backward($this->data->training->output[$rand_index]);
    }
  }

  public function set_learning_rate ($rate) {
    $this->learning_rate = $rate ? $rate : 0.1;
  }

  private function set_weights_and_biases () {
    for ($layer=0; $layer<$this->layers-1; $layer++) {
      $rows = $this->nodes[$layer+1];
      $cols = $this->nodes[$layer];
      $this->weights[$layer] = new Matrix ($rows, $cols);
      $this->weights[$layer]->randomize(-1, 1);
      $this->biases[$layer] = new Matrix ($rows, 1);
      $this->biases[$layer]->randomize(-1, 1);
    }
  }

  private function update_weights_and_biases ($layer) {
    $gradient = Matrix::copy($this->calculate[$layer]);
    $gradient->map(function ($v, $i, $j) {return 1 - $v * $v;});

    $delta = Matrix::mult($this->errors[$layer], $this->learning_rate);
    $delta->hadamard($gradient);

    $transpose_last = Matrix::transpose($this->calculate[$layer-1]);
    $this->weights[$layer-1]->add(Matrix::mult($delta, $transpose_last));
    $this->biases[$layer-1]->add($delta);
  }
}
?>
