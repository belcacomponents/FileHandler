<?php

namespace Belca\FileHandler;

use Belca\FileHandler\FileHandlerTrait;

// TODO расширить абстрактным классом, а здесь реализовать обработку handle,
// setHandlerParameters
/**
 * Сохраняет и обрабатывает сохраненный файл создавая модификации файлов.
 */

class FileHandler extends FileHandlerAbstract
{
    use FileHandlerTrait;
    // TODO только базовая реализация без создания модификаций, но с попыткой запуска.
    //
    // TODO предоставляет функции-интерфейсы для загрузки и обработки файлов
    // 1. сохранить по указанному пути
    // 2. обработать по заданным правилам
    // 3. вернуть пути к файлам
    // 4. вернуть путь к оригиналу


}
