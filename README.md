# php-neural-network

<a name="index_block"></a>

* [1. Fully Connected Neural Network (FCNN)](#block1)
    * [1.1. Build the network](#block1.1)
    * [1.2. Set data](#block1.2)
        * [1.2.1. Training data](#block1.2.1)
        * [1.2.2. Controle data](#block1.2.2)
    * [1.3. Set activation function](#block1.3)
    * [1.4. Set learning rate](#block1.4)
    * [1.5. Train the network](#block1.5)
    * [1.6. Controle the effectiveness](#block1.6)
    * [1.7. Predict values from new inputs](#block1.7)
    * [1.8. Save state](#block1.8)
    * [1.9. Load state](#block1.9)


<a name="block1"></a>
## 1. Fully Connected Neural Network (FCNN) [↑](#index_block)



<a name="block1.1"></a>
### 1.1. Build the network [↑](#index_block)

The FCNN class can be contruct by placing a single array of integers as parameter or multiple interger parameters, each integer represent the number of neurons (perceptrons) in each layer of the network, the first integer should always be equal to the number of inputs passed into calculation and the last integer should always be equal to the number of expected outputs.

```php
<?php
include ("php-neural-network/FCNN.php"); //
$brain = new FCNN (2, 3, 1);
?>
```

<a name="block1.2"></a>
### 1.2. Set data [↑](#index_block)

Two methods can be called to set known data, both require an indexed array containing a list of associatives arrays. Each associative array must contains two keys: "input" that contains an array of integer or floating values given as inputs, and "output" that contains the expected outcome (here again the value(s) must be wrap into an array).
```php
<?php
// Exemple of data for the "XOR" function
$XOR = array(
  ['input' => [0, 0], 'output' => [0]],
  ['input' => [0, 1], 'output' => [1]],
  ['input' => [1, 0], 'output' => [1]],
  ['input' => [1, 1], 'output' => [0]]
);
?>
```
The input layer contains a flattening algorithm, which mean that multi-dimentional arrays are accepted as input.

<a name="block1.2.1"></a>
#### 1.2.1. Training data [↑](#index_block)

To set the data that will later be use to train the neural network, use the following method:

```php
<?php
$brain->set_data_training($XOR);
?>
```

<a name="block1.2.2"></a>
#### 1.2.2. Controle data [↑](#index_block)

To set the data that will later be use to controle if the training worked, use the following method:

```php
<?php
$brain->set_data_controle($XOR);
?>
```

<a name="block1.3"></a>
### 1.3. Set activation function [↑](#index_block)

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


<a name="block1.4"></a>
### 1.3. Set learning rate [↑](#index_block)

The learning rate is use to limit the modification brought to weights and biases when updated

```php
<?php
$brain->set_learning_rate(0.05);
?>
```

<a name="block1.5"></a>
### 1.5. Train the network [↑](#index_block)

Once the neural network is setup, the training method will ajust all internal weights and biases to find an approximation of desired function.

```php
<?php
$brain->train(1000);
?>
```

The train function take a single integer parameter which represent the number of time the network will calculate a training input nd correct his weights and biases depending on the error between the result it obtain and the expected output. Each time, one of the rows from training data is randomly selected.

<a name="block1.6"></a>
### 1.6. Controle the effectiveness [↑](#index_block)

The controle method verify if the training worked, all rows are calculated one by one and the network check if the error between its result is lower than the maximum error accepted (given as parameter). The method return a boolean value.

```php
<?php
$brain->controle(0.001);
?>
```

<a name="block1.7"></a>
### 1.7. Predict values from new inputs [↑](#index_block)

Once the training is succesful, you can simply use the neural network to predict an output for any given inputs.

```php
<?php
echo $brain->calculate([0.25, 0.25]);
echo $brain->calculate([0.25, 0.75]);
echo $brain->calculate([0.75, 0.25]);
echo $brain->calculate([0.75, 0.75]);
?>
```

<a name="block1.8"></a>
### 1.8. Save state [↑](#index_block)

The save method will save the structure of the neural network as well as all the weights and biases values in json format. The parameter is the naame of the file.

```php
<?php
$brain->save("XOR");
?>
```

<a name="block1.9"></a>
### 1.9. Load state [↑](#index_block)

The load static function aloud you to retrieve a saved neural network

```php
<?php
$brain = FCNN::load("XOR");
?>
```










