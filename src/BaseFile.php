<?php

namespace Mia\Installer;

abstract class BaseFile 
{
    /**
     * Path del archivo a tener de base
     * @var string
     */
    protected $filePath = '';
    /**
     * Almacena el contenido del archivo
     * @var string
     */
    protected $file = '';
    /**
     * Path de la carpeta donde se va a guardar
     * @var string
     */
    protected $savePath = '';
    
    public function __construct()
    {
        // Abrir el archivo
        $this->openFile();
    }

    abstract function run();
    
    protected function openFile()
    {
        $this->file = file_get_contents( str_replace( 'agencycoda', 'doroteati', $this->filePath ) );
    }
    /**
     * Convierte el texto en camelcase
     * 
     * Ej: blog_tag => BlogTag
     * Ej: blog-tag => BlogTag
     *
     * @param string $text
     * @return string
     */
    protected function getCamelCase($text)
    {
        return str_replace(' ', '', ucwords(str_replace(['_', '-'], [' ', ' '], $text)));
    }
    /**
     * Convierte el texto en camelcase
     * 
     * Ej: blog_tag => blogTag
     * Ej: blog-tag => blogTag
     *
     * @param string $text
     * @return string
     */
    protected function getCamelCaseVar($text)
    {
        return str_replace(' ', '', 
                lcfirst(ucwords(str_replace(['_', '-'], [' ', ' '], $text)))
        );
    }
}
