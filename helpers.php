<?php

/**
 * Get the base path
 *
 * @param string $path
 * @return string
 */
function basePath($path)
{
    return __DIR__ . '/' . $path;
}

/**
 * Load a view
 * @param string $name
 * @return void
 */
function loadView($name, $data = [])
{
    $viewPath = basePath("App/views/{$name}.view.php");
    if (!file_exists($viewPath)) {
        echo "View {$name} not found";
        return;
    }

    extract($data);
    require $viewPath;
}

/**
 * Load a partial
 * @param string $name
 * @return void
 */
function loadPartial($name)
{
    $partialPath = basePath("App/views/partials/{$name}.php");
    if (!file_exists($partialPath)) {
        echo "Partial {$name} not found";
        return;
    }

    require $partialPath;
}


/**
 * Inspect a value
 * @param mixed $value
 * @return void
 */
function inspect($value)
{
    echo '<pre>';
    var_dump($value);
    echo '</pre>';
}

/** Inspect a value and die
* @param mixed $value
* @return void
*/
function inspectAndDie($value)
{
    echo '<pre>';
    var_dump($value);
    echo '</pre>';
    die();
}

/**
 * Format salary
 * @param string $salary
 * @return string Formatted salary
 */
function formatSalary($salary)
{
    return '$' . number_format(floatval($salary), 0, ',');
}

/**
 * Sanitize Data
 * @param string $dirty
 * @return string
 */
function sanitize(string $dirty)
{
    return filter_var(trim($dirty), FILTER_SANITIZE_SPECIAL_CHARS);
}

/**
 * Redirect to a given url
 * @param string $url
 * @return void
 */
function redirect($url)
{
    header("location: {$url}");
    exit();
}