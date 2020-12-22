# php-neural-network

```php
<?php
include ("php-neural-network/FCNN.php"); //


$brain = new FCNN (2, 3, 1);

$XOR = array(
  [input => [0, 0], output => [0]],
  [input => [0, 1], output => [1]],
  [input => [1, 0], output => [1]],
  [input => [1, 1], output => [0]],
);

$brain->set_data_training($XOR);
$brain.train(1000);

echo $brain->caluculate([0.75, 0.25]);

?>
```
