<?php


$patterns = [
  
  /**
   * Here are patterns for conditionals.
   */
  'if'     => '/@if\(([^@\{]+)\)/',
  'elseif' => '/@elseif\(([^@\{]+)\)/',
  'else'   => '/@else/',
  'endif'  => '/@endif/',


  /**
   * Here are patterns for loops.
   */

  'foreach'    => '/@foreach\(([^@\{]+)\s+as\s+([^@\{]+)\)/',
  'endforeach' => '/@endforeach/',
  'for'        => '/@for\(([^@\{]+)\)/',
  'endfor'     => '/@endfor/',
  'while'      => '/@while\(([^@\{]+)\)/',
  'endwhile'   => '/@endwhile/',


  /**
   * Here are the interploation patterns.
   */

  'interpolation_safe' => '/\{\{([^\}]+)\}\}/',
  'interpolation'      => '/\{\!\!([^\!]+)\!\!\}/',


  /**
   * Here are patterns for including other views.
   */

   'include' => '/@include\(([^@\{]+)\)/',
   'content' => '/@content/',


   /**
    * Here is the pattern for extending a layout.
    */

   'extends' => '/@extends\(([^@\{]+)\)/',

];


$replacements = [
  
  /**
   * Here are replacements for conditional patterns.
   */

  'if'     => 'if($1):',
  'elseif' => 'elseif($1):',
  'else'   => 'else:',
  'endif'  => 'endif;',


  /**
   * Here are replacements for loop patterns.
   */

  'foreach'    => 'foreach($1 as $2):',
  'endforeach' => 'endforeach;',
  'for'        => 'for($1):',
  'endfor'     => 'endfor;',
  'while'      => 'while($1):',
  'endwhile'   => 'endwhile;',


  /**
   * Here are replacements for interpolation patterns.
   */

  'interpolation_safe' => 'echo htmlspecialchars($1);',
  'interpolation'      => 'echo ($1);',


  /**
   * Here are replacements for include patterns.
   */

   'include' => 'require $__includes__ . "includes.$1.php";',
   'content' => 'require $__content__;',


   /**
    * Here is replacement for the extend pattern.
    */

   'extends' => '//This view extends $1 layout',

];
