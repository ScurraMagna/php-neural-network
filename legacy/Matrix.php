<?php

class Matrix {

  private $cols;
  private $rows;

  private $data = [];

  /**
   * create an instance of Matrix.
   * @param {int} $rows number of rows
   * @param {int} $columns number of columns
   */
  public function __construct (int $rows, int $cols) {
    $this->rows = $rows;
    $this->cols = $cols;
    $this->data = array_fill(0, $rows*$cols, 0);
  }

  /**
   * turn two dimentions indexes to one dimention
   * @param {int} $i row index
   * @param {int} $j column index
   * @return {int} one dimention index
   */
  private function index ($i, $j) {
    return $i * $this->cols + $j;
  }

  /**
   * get the value at row index $i and column index $j
   * @param {int} $i row index
   * @param {int} $j column index
   * @return {int|double} value of cell[$i][ĵ]
   */
  public function get (int $i, int $j) {
    return $this->data[$this->index($i, $j)];
  }

  /**
   * set the value at row index $i and column index $j
   * @param {int} $i row index
   * @param {int} $j column index
   */
  public function set ($i, $j, $value) {
    $this->data[$this->index($i, $j)] = $value;
  }

  /**
   * run through each cell of the matrix and apply the
   * function given as parameter, if that function return
   * a value, that value will overwrite the value of the cell
   * @param {function} $callback function to apply
   */
  public function map ($callback) {
    for ($i=0; $i<$this->rows; $i++) {
      for ($j=0; $j<$this->cols; $j++) {
        $value = $this->get($i, $j);
        $this->set($i, $j, $callback($value, $i, $j));
      }
    }
  }

  /**
   * create a Matrix instance from array given as parameter
   * @param {array} $array array of 1 or 2 dim containing numbers
   * @return {Matrix} Matrix object
   */
  static function from_array ($array) {
    $cols = is_array($array) ? count($array[0]) : 1;
    $result = new Matrix (count($array), $cols);
    $result->map(function ($v, $i, $j) {
      return is_array($array[0]) ? $array[$i][$j] : $array[$i];
    });
    return $result;
  }

  /**
   * @return {array} an array representation of the Matrix
   */
  public function to_array () {
    $array = [];
    for ($i=0; $i<$this->rows; $i++) {
      $array[$i] = []
      for ($j=0; $j<$this->cols; $j++) {
        $array[$i][$j] = $this->get($i, $j);
      }
    }
    if (count($array) <= 1) {
      $array = $array[0];
    }
    return $array;
  }

  /**
   * create a matrix that clne the matrix given as parameter
   * @param {Matrix} $matrix
   * @return {Matrix} clone of $matrix
   */
  static function copy ($matrix) {
    $result = new Matrix ($matrix->rows, $matrix->cols);
    $result->map(function ($v, $i, $j) {
      return $matrix->get($i, $j);
    });
    return $result;
  }

  /**
   * modify the matrix to become a replica of the matrix
   * given as parameter
   * @param {Matrix} $matrix
   */
  public function copy ($matrix) {
    $this->rows = $matrix->rows;
    $this->cols = $matrix->cols;
    $this->data = $matrix->data;
    return $this;
  }

  /**
   * seed the matrix with random values between the two parameters
   * @param {int|double} $from smallest value accepted
   * @param {int|double} $to greatest value accepted
   */
  public function randomize ($from, $to) {
    $this->map(function ($v, $i, $j) {
      return (rand() / getrandmax()) * ($to - $from) + $from;
    });
  }



  /**
   * return matrix product of matrix $A and matrix $B
   * @param {Matrix} $A
   * @param {Matrix} $B
   * @return {Matrix} $A . $B
   */
  static function mult ($A, $B) {
    if ($A->cols != $B->rows) {return false;}
    $cols = ($B instanceof Matrix) ? $B->cols : $A->cols;
    $result = new Matrix($A->rows, $cols);
    if ($B instanceof Matrix) {
      $result->map(function($v, $i, $j) {
        $sum = 0;
        for ($k=0; $k<$A->cols; $k++) {
          $sum += $A->get($i, $k) * $B->get($k, $j);
        }
        return $sum;
      });
    }
    else {
      $result->map(function($v, $i, $j) {
        return $A->get($i, $j) * $B;
      });
    }
    return $result;
  }

  /**
   * multiply the matrix given as parameter to $this (matrix product)
   */
  public function mult ($value) {
    $result = Matrix::mult($this, $value);
    if ($result) {$this->copy($result);}
    return $this;
  }

  /**
   * return cellwise product of matrix $A and matrix $B
   * @param {Matrix} $A
   * @param {Matrix} $B
   * @return {Matrix} $A * $B
   */
  static function hadamard ($A, $B) {
    if (!Matrix::size($A, $B)) {return false;}
    $result = new Matrix($A->rows, $A->cols);
    $result->map(function ($v, $i, $j) {
      return $A->get($i, $j) * $B->get($i, $j);
    });
    return $result;
  }

  /**
   * multiply the matrix given as parameter to $this (cellwise)
   */
  public function hadamard ($value) {
    $result = Matrix::hadamard($this, $value);
    if ($result) {$this->copy($result);}
    return $this;
  }

  /**
   * return the sum of matrix $A and matrix $B
   * @param {Matrix} $A
   * @param {Matrix} $B
   * @return {Matrix} $A + $B
   */
  static function add ($A, $B) {
    if (!Matrix::size($A, $B)) {return false;}
    $result = new Matrix($A->rows, $A->cols);
    $result->map(function ($v, $i, $j) {
      return ($B instanceof Matrix) ?
             $A->get($i, $j) + $B->get($i, $j) :
             $A->get($i, $j) + $B;
    });
    return $result;
  }

  /**
   * add the matrix given as parameter to $this
   * @param {Matrix} $value
   */
  public function add ($value) {
    $result = Matrix::add($this, $value);
    if ($result) {$this->copy($result);}
    return $this;
  }

  /**
   * return the difference between matrix $A and matrix $B
   * @param {Matrix} $A
   * @param {Matrix} $B
   * @return {Matrix} $A - $B
   */
  static function sub ($A, $B) {
    if (!Matrix::size($A, $B)) {return false;}
    $result = new Matrix($A->rows, $A->cols);
    $result->map(function ($v, $i, $j) {
      return ($B instanceof Matrix) ?
             $A->get($i, $j) - $B->get($i, $j) :
             $A->get($i, $j) - $B;
    });
    return $result;
  }

  /**
   * subtract the matrix given as parameter from $this
   * @param {Matrix} $value
   */
  public function sub ($value) {
    $result = Matrix::sub($this, $value);
    if ($result) {$this->copy($result);}
    return $this;
  }



  /**
   * @return sum of all cells
   */
  public function grand_sum () {
    $sum = 0;
    for ($i=0; $i<$this->rows; $i++) {
      for ($j=0; $j<$this->cols; $j++) {
        $sum += $this->get($i, $j);
      }
    }
    return $sum;
  }

  /**
   * @return sum of diagonal values
   */
  public function trace () {
    if (!Matrix::square($this)) {return false;}
    $sum = 1;
    for ($i=0; $i<$this->rows; $i++) {
      $sum += $this->get($i, $i);
    }
    return $sum;
  }

  /**
   * @return {double} determinant of Matrix
   */
  public function det () {
    if (!Matrix::square($this)) {return false;}
    if (!isset($this->upper)) {$this->LU();}
    $result = 1;
    for ($i=0; $i<$this->rows; $i++) {
      $result *= $this->upper->get($i, $i);
    }
    return $result;
  }

  /**
   * use LU decomposition to create two triangular matrices
   * such that A = LU where L is a lower triangular matrix and
   * U a upper trianguar matrix, they are stored in two variables
   * $this->lower and $this->upper
   */
  public function LU () {
    if (!Matrix::square($this)) {return false;}
    $L = new Matrix ($this->rows, $this->cols);
    $U = new Matrix ($this->rows, $this->cols);
    for ($i=0; $i<$this->rows; $i++) {
      for ($k=$i; $k<$this->cols; $k++) {
        $sum = 0;
        for ($j=0; $j<$i; $j++) {
          $sum += $L->get($i, $j) * $U->get($j, $k);
        }
        $U->set($i, $k, $this->get($i, $k) - $sum);
      }
      for ($k=$i; $k<$this->cols; $k++) {
        if ($i==$k) {$L->set($k, $i, 1);}
        else {
          $sum = 0;
          for ($j=0; $j<$i; $j++) {
            $sum += $L->get($k, $j) * $U->get($j, $i);
          }
          $L->set($k, $i, ($this->get($k, $i) - $sum) / $U->get($i, $i));
        }
      }
    }
    $this->lower = $L;
    $this->upper = $U;
  }



  /**
   * return a transposed version of the matrix given
   * as parameter
   * @param {Matrix} $matrix
   * @return {Matrix} transposed matrix
   */
  static function transpose ($matrix) {
    $result = new Matrix($matrix->cols, $matrix->rows);
    $result->map(function($v, $i, $j) {
      return $matrix->get($j, $i);
    });
    return $result;
  }

  /**
   * transpose the Matrix
   */
  public function transpose () {
    $this->copy(Matrix::transpose($this));
    return $this;
  }

  /**
   * return the invert matrix of the matrix given as parameter
   * @param {Matrix} $matrix 
   * @return {Matrix} invert of $matrix
   */
  static function invert ($A) {
    if (!Matrix::invertible($A)) {return false;}
    if (!isset($A->upper)) {$A->LU();}
    $iL = new Matrix ($A->rows, $A->cols);
    $iL->map(function ($v, $i, $j) {
      $sum = 0;
      for ($k=0; $k<$A->rows; $k++) {
        $sum += $k != $i ? $A->lower->get($i, $k) * $iL->get($k, $j) : 0;
      }
      return $i == $j ? 1 - $sum : -$sum;
    });
    $iU = new Matrix ($A->rows, $A->cols);
    for ($j=0; $j<$iU->rows; $j++) {
      $iU->set($j, $j, 1/$A->upper->get($j, $j));
      for ($i=0; $i<$j; $i++) {
        $sum = 0;
        for ($k=0; $k<$j; $k++) {
          $sum -= $iU->get($i, $k) * $A->upper->get($k, $j);
        }
        $iU->set($i, $j, $sum / $A->upper->get($j, $j));
      }
    }
    return Matrix::mult($iU, $iL);
  }

  /**
   * return the identity matrix of size $n*$n
   * @return {Matrix} identity matrix
   */
  static function identity ($n) {
    $result = new Matrix ($n, $n);
    $result->map(function ($v, $i, $j) {
      return $i === $j ? 1 : 0;
    });
    return $result;
  }

  /**
   * return the exponential function of a matrix
   * @param {Matrix} $matrix Matrix object
   * @return {Matrix} exponential of $matrix 
   */
  static function exp ($matrix) {
    if (!Matrix::square($matrix)) {return false;}
    $sum = Matrix::identity($matrix->rows);
    $last = new Matrix($matrix->rows, $matrix->rows);
    $num = Matrix::identity($matrix->rows);
    $count = 1; $den = 1;
    while (!Matrix::equals($sum, $last)) {
      $last = Matrix::copy($sum);
      $num->mult($matrix);
      $den *= $count;
      $sum->add(Matrix::mult($num, 1/$den));
      $count++;
    }
    return $sum;
  }

  /**
   * return the cosinus function of a matrix
   * @param {Matrix} $matrix Matrix object
   * @return {Matrix} cosinus of $matrix 
   */
  static function cos ($matrix) {
    if (!Matrix::square($matrix)) {return false;}
    $sum = Matrix::identity($matrix->rows);
    $last = new Matrix($matrix->rows, $matrix->rows);
    $num = Matrix::identity($matrix->rows);
    $count = 1; $den = 1; $sgn = 1;
    while (!Matrix::equals($sum, $last)) {
      $last = Matrix::copy($sum);
      $num->mult(Matrix::mult($matrix, $matrix));
      $den *= 4 * $count * $count - 2 * $count;
      $sgn *= -1;
      $sum->add(Matrix::mult($num, $sgn/$den));
      $count++;
    }
    return $sum;
  }

  /**
   * return the sinus function of a matrix
   * @param {Matrix} $matrix Matrix object
   * @return {Matrix} sinus of $matrix 
   */
  static function sin ($matrix) {
    if (!Matrix::square($matrix)) {return false;}
    $sum = Matrix::copy($matrix);
    $last = Matrix::identity($matrix->rows);
    $num = Matrix::copy($matrix);
    $count = 1; $den = 1; $sgn = 1;
    while (!Matrix::equals($sum, $last)) {
      $last = Matrix::copy($sum);
      $num->mult(Matrix::mult($matrix, $matrix));
      $den *= 4 * $count * $count + 2 * $count;
      $sgn *= -1;
      $sum->add(Matrix::mult($num, $sgn/$den));
      $count++;
    }
    return $sum;
  }



  /**
   * check if the two matrices given as parameters are the same
   * size. i.e. they have the same amount of rows and columns
   */
  static function size ($A, $B) {
    return $A->rows == $B->rows && $A->cols == $B->cols;
  }

  /**
   * check if the two matrices given as parameters are equals
   * i.e. they have the same amount of rows and columns and
   * each cells has the same value
   */
  static function equals ($A, $B) {
    return $A->data == $B->data && Matrix::size($A, $B);
  }

  /**
   * check if the matrix given as parameter is a square matrix
   * i.e. it has the same amount of rows as columns
   */
  static function square ($A) {
    return $A->rows == $A->cols;
  }

  /**
   * check if the matrix given as parameter is symmetric
   * i.e. the matrix is equals to its transposed matrix
   */
  static function symmetric ($A) {
    if (!Matrix::square($A)) {return false;}
    $T = Matrix::transpose($A);
    return Matrix::equals($A, $T);
  }

  /**
   * check if the matrix given as parameter is orthogonal
   * i.e. the invert of matrix is equals to its transposed matrix
   */
  static function orthogonal ($A) {
    if (!Matrix::square($A)) {return false;}
    $T = Matrix::transpose($A);
    $I = Matrix::invert($A)
    return Matrix::equals($T, $I);
  }

  /**
   * check if the matrix given as parameter is invertible.
   * a matrix is invertible if and only if its determinant is not null
   */
  static function invertible ($A) {
    if (!Matrix::square($A)) {return false;}
    return $A->det() != 0;
  }

}

?>
