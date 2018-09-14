<?php

/**
 * This class is used as application's templating engine.
 */

class TemplateEngine
{

  public static $viewsSource;
  public static $viewsOutput;
  private static $rules = null;
  public static $mode = 'DEVELOPMENT';
  private static $views = [];


  /**
   * Renders the provided view. If app is in DEV mode, view
   * and its dependencies are first compiled if needed.
   * 
   * @var string view
   * @var array  data
   * 
   * @return void
   */

  public static function render($view, $data = []) 
  {
    if(self::$mode === 'DEVELOPMENT') {
      $views = self::getViewDependencies($view);
      $layout = self::getViewLayout($view);
      $data['__includes__'] = self::$viewsOutput;
      if($layout) {
        $views = array_merge(
          $views, self::getViewDependencies($layout)
        );
        $data['__content__'] = self::$viewsOutput . "$view.php";
        $view = $layout;
      }
      foreach($views as $single) {
        if(self::viewOutdated($single)) {
          self::compile($single);
        }
      }
    }

    extract($data);

    require self::$viewsOutput . "$view.php";
  }


  /**
   * Compiles the provided file.
   * 
   * @var string view
   * 
   * @return void
   */

  public static function compile($view) 
  {
    $rules = self::getRules();
    $patterns = $rules['patterns'];
    $replacements = $rules['replacements'];

    $realViewPath = str_replace('.', '/', $view);

    $input = fopen(self::$viewsSource ."$realViewPath.php", 'r');
    $output = fopen(self::$viewsOutput ."$view.php", 'w');
  
    while($line = fgets($input)) {
      fputs($output, self::compileLine($line, $patterns, $replacements));
    }
  
    fclose($input);
    fclose($output);
  }


  /**
   * Compiles a single line of input.
   * 
   * @var string line
   * @var array  conditions
   * @var array  replacements
   * 
   * @return string compiledLine
   */

  private static function compileLine($line, $patterns, $replacements) 
  {
    foreach($patterns as $key => $condition) {
      $replacement = '<?php '. $replacements[$key] .' ?>';
      $line = preg_replace($condition, $replacement, $line);
    }
    return $line;
  }


  /**
   * Checks if a view has been modified after the last compilation
   * or if a compiled version does not exist.
   * 
   * @var string view
   * 
   * @return bool
   */

   private static function viewOutdated($view)
   {
     //If compiled does'nt exist, view is considred outdated
     if(!file_exists(self::$viewsOutput . "$view.php")) return true;

     $realViewPath = str_replace('.', '/', $view);

     $modifiedAt = filemtime(self::$viewsSource . "$realViewPath.php");
     $compiledAt = filemtime(self::$viewsOutput . "$view.php");

     return $modifiedAt > $compiledAt;
   }


   /**
    * Recursively searches for all includes in a provided view
    * and returns an array containing each of them.
    * 
    * @var string view
    *
    * @return array
    */

  private static function getViewDependencies($view)
  {
    $rules = self::getRules();
    $includePattern = $rules['patterns']['include'];
    $dependencies = [$view];
    $file = self::getViewSourceFile($view);

    if(preg_match_all($includePattern, $file, $matches)) {
      foreach($matches[1] as $single) {
        $dependencies = array_merge(
          $dependencies, self::getViewDependencies('includes.'. $single)
        );
      }
    }
    return $dependencies;
  }


  /**
   * Returns a filename of layout view which the provided view extends.
   * 
   * @var string view
   * 
   * @return mixed layout|false
   */

  private static function getViewLayout($view)
  {
    $rules = self::getRules();
    $extendPatern = $rules['patterns']['extends'];
    $file = self::getViewSourceFile($view);
    if(preg_match($extendPatern, $file, $matches)) {
      return 'layouts.' . $matches[1];
    } else {
      return false;
    }
  }


  /**
   * Returns the content of provided view for compilation.
   * 
   * @var string view
   * 
   * @return string text
   */

  public static function getViewSourceFile($view)
  {
    if(isset(self::$views[$view])) return self::$views[$view];

    $realViewPath = str_replace('.', '/', $view);
    self::$views[$view] = file_get_contents(
      self::$viewsSource . "$realViewPath.php"
    );

    return self::$views[$view];
  }


  /**
   * Returns the template engine patterns and replacements.
   * 
   * @return array
   */

  private static function getRules()
  {
    if(self::$rules) return self::$rules;

    require __DIR__ . '/rules.php';

    self::$rules = [
    'patterns' => $patterns, 'replacements' => $replacements
    ];

    return self::$rules;
  }


  /**
   * Deletes compiled views and compile timestamps.
   * 
   * @return void
   */

  public static function flush()
  {
    file_put_contents(__DIR__ . '/compiled_at.json', '{}');
    $views = array_diff(scandir(self::$viewsOutput), ['.', '..']);
    foreach($views as $view) unlink(self::$viewsOutput . $view);
  }

}