<?php

namespace Inc\Base;

class Methods
{
  public function getP24ID()
  {
    $value = esc_attr( get_option('text_example') );
    echo $value;

  }
}
