<?php

class FCNN {

  private $nodes;
  private $count;

  private $layers;

  private $training_data = [];
  private $controle_data = [];

  public function __construct ($nodes) {
    $nodes = is_array($nodes) ? $nodes : func_get_args();
    $this->nodes = $nodes;
    $this->count = count($nodes);

    require_once("src/DenseLayer.php");
    require_once("src/DenseLayerInput.php");
    require_once("src/DenseLayerOutput.php");
    
    $this->layers[0] = new DenseLayerInput ();
    for ($i=1; $i<$this->count; $i++) {
      $this->layers[$i] = ($i == $this->count-1) ?
                          new DenseLayerOutput ($nodes[$i-1], $nodes[$i]) :
                          new DenseLayer ($nodes[$i-1], $nodes[$i]);
    }
  }

  public function calculate ($input) {
    for ($i=0; $i<$this->count; $i++) {
      if ($i === 0) {
        $this->layers[$i]->forward($input);
      }
      else {
        $this->layers[$i]->forward($this->layers[$i-1]->forward_output);
      }
    }
    return $this->layers[$this->count-1]->forward_output;
  }

  public function train ($epoch) {
    for ($i=0; $i<$epoch; $i++) {
      $row = $this->random_training_data();
      $this->calculate($row->input);
      for ($i=$this->count-1; $i<=0; $i--) {
        if ($i === $this->count-1) {
          $this->layers[$i]->backward($row->output);
        }
        else {
          $this->layers[$i]->backward($this->layers[$i+1]->backward_output);
        }
      }
    }
  }

  public function set_data_training($data) {
    $this->training_data->input = array();
    $this->training_data->output = array();
    for ($i=0; $i<count($data); $i++) {
      $this->training_data->input[$i] = data[$i]["input"];
      $this->training_data->output[$i] = data[$i]["output"];
    }
  }

  public function controle ($max_error) {
    foreach ($this->controle_data as $row) {
      $this->calculate($row->input);
      $this->layers[$this->count-1]->backward($row->output)
      $check = $this->check_error($max_error, $this->layers[$this->count-1]->backward_output);
      if (!$check) {return false;}
    }
    return true;
  }

  public function set_data_controle($data) {
    $this->controle_data->input = array();
    $this->controle_data->output = array();
    for ($i=0; $i<count($data); $i++) {
      $this->controle_data->input[$i] = data[$i]["input"];
      $this->controle_data->output[$i] = data[$i]["output"];
    }
  }

  private function random_training_data () {
    $index = round(rand() / getmaxrand() * (count($this->training_data) - 1));
    return $this->training_data[$index];
  }

  private function check_error ($max_error, $computed) {
    for ($i=0; $i<count($computed); $i++) {
      if ($computed[$i] > $max_error) {return false;}
    }
    return true;
  }

  public function set_activation (string $functionName) {
    foreach ($this->Layers as $layer) {
      $layer->set_activation_function($functionName);
    }
  }

}

?>
