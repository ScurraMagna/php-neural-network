# php-neural-network

<a name="index_block"></a>

* [1. Fully Connected Neural Network (FCNN)](#block1)
    * [1.1. Set data](#block1.1)
        * [1.1.1. Training data](#block1.1.1)
        * [1.1.2. Controle data](#block1.1.1)
    * [1.2. Set activation function](#block1.2)


<a name="block1"></a>
## 1. Fully Connected Neural Network (FCNN) [↑](#index_block)

```php
<?php
include ("php-neural-network/FCNN.php"); //
$brain = new FCNN (2, 3, 1);
?>
```

<a name="block1.1"></a>
### 1.1. Set data [↑](#index_block)

Two methods can be called to set known data, both require an indexed array containing a list of associatives arrays. Each associative array must contains two keys: "input" that contains an array of integer or floating values given as inputs, and "output" that contains the expected outcome (here again the value(s) must be wrap into an array).
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
The input layer contains a flattening algorithm, which mean that multi-dimentional arrays are accepted as input.

<a name="block1.1.1"></a>
#### 1.1.1. Training data [↑](#index_block)

To set the data that will later be use to train the neural network, use the following method:

```php
<?php
$brain->set_data_training($XOR);
?>
```

<a name="block1.1.2"></a>
#### 1.1.1. Controle data [↑](#index_block)

To set the data that will later be use to controle the neural network, use the following method:

```php
<?php
$brain->set_data_controle($XOR);
?>
```

<a name="block1.2"></a>
### 1.2. Set activation function [↑](#index_block)

In order to be able to resolve non linear problems, an activation function must be set:

```php
<?php
$brain->set_activation("tanh");
?>
```

the unique parameter must be a sting, four values are recognized:

```"tanh"``` : hyperbolic tangent function

```"sigmoid"``` : Sigmoid function

```"ReLU"``` : Rectified Linear Unit function

```"SiLU"``` : Sigmoid Linear Unit function

