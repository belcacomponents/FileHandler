<?php

namespace Belca\FileHandler;

/**
 * Выполняет вспомогательную обработку данных.
 */
class FileHandlerUtility
{
    /**
     * Объедиянет переданные правила обработчика указанным методом и
     * возвращает их.
     *
     * @param  string $method
     * @param  mixed  $rules
     * @return mixed
     */
    public static function mergeRules($method, ...$rules)
    {
        switch ($method) {
          case 'reset':
            return [];
            break;

          case 'merge':
          default:
            return array_merge($rules);
            break;
        }
    }

    /**
     * Объединяет переданные скрипты указанным методом слияния и возвращает их.
     *
     * @param  string $method
     * @param  mixed  $scripts
     * @return mixed
     */
    public static function mergeScripts($method, ...$scripts)
    {
        switch ($method) {
          case 'reset':
            return [];
            break;

          case 'merge':
          default:
            return array_merge($scripts);
            break;
        }
    }

    /**
     * Объединяет массивы имен свойств по указанному методу объединения.
     *
     * @param  string $method
     * @param  array  $names
     * @return array
     */
    public static function mergePropertyNames($method, ...$names)
    {
        switch ($method) {
          case 'reset':
            return [];
            break;

          case 'merge':
          default:
            return array_merge($names);
            break;
        }
    }
}
