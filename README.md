# php-neural-network

<a name="index_block"></a>

* [1. Fully Connected Neural Network (FCNN)](#block1)
    * [1.1. Set data](#block1.1)


## 1. Fully Connected Neural Network (FCNN) [↑](#index_block)

```php
<?php
include ("php-neural-network/FCNN.php"); //


$brain = new FCNN (2, 3, 1);

$XOR = array(
  [input => [0, 0], output => [0]],
  [input => [0, 1], output => [1]],
  [input => [1, 0], output => [1]],
  [input => [1, 1], output => [0]]
);

$brain->set_data_training($XOR);
$brain.train(1000);

echo $brain->caluculate([0.75, 0.25]);

?>
```
### 1.1 Set data [↑](#index_block)

Two methods can be called to set known data, both require an indexed array containing a list of associatives arrays. Each associative array must contains two keys: "input" that contains an array of integer or floating values given as inputs, and "output" that contains the expected outcome (here again the value(s) must be wrap into an array) as follow:
```php
<?php
// Exemple of data for the "XOR" function
$XOR = array(
  [input => [0, 0], output => [0]],
  [input => [0, 1], output => [1]],
  [input => [1, 0], output => [1]],
  [input => [1, 1], output => [0]]
);
?>
```
