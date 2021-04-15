<?php

namespace Mia\Installer\Generate\Mezzio;

use Mia\Installer\BaseFile;
use \Illuminate\Database\Capsule\Manager as DB;
class Model extends BaseFile
{
    /**
     * Path del archivo a tener de base
     * @var string
     */
    protected $filePath = './vendor/agencycoda/mia-installer-mezzio/data/mezzio/g_model.txt';
    /**
     * Path de la carpeta donde se va a guardar
     * @var string
     */
    protected $savePath = './src/App/src/Model/';
    /**
     * Nombre de la DB
     *
     * @var string
     */
    public $name = '';

    public function run()
    {
        // Obtener las columnas de la tabla
        $columns = DB::select('DESCRIBE ' . $this->name);
        // Verificar si activar timestamp
        $hasTimestamp = false;
        // Verificar si tiene Deleted
        $hasDeleted = false;
        // Recorremos las columnas
        $swagger = '';
        $properties = '';
        foreach($columns as $column){
            $typeSwagger = '';
            if(stripos($column->Type, 'int') !== false){
                $typeSwagger = 'integer';
            }else if(stripos($column->Type, 'varchar') !== false||stripos($column->Type, 'text') !== false){
                $typeSwagger = 'string';
            }else if(stripos($column->Type, 'decimal') !== false||stripos($column->Type, 'float') !== false||stripos($column->Type, 'double') !== false){
                $typeSwagger = 'number';
            }

            $swagger .= ' * @OA\Property(
 *  property="'.$column->Field.'",
 *  type="'.$typeSwagger.'",
 *  description=""
 * )
';
            if($column->Field == 'id'){
                $properties .= ' * @property int $'.$column->Field.' ID of item
';
                continue;
            } else { 
                $properties .= ' * @property mixed $'.$column->Field.' Description for variable
';
            }

            if($column->Field == 'created_at'){
                $hasTimestamp = true;
            }

            if($column->Field == 'deleted'){
                $hasDeleted = true;
            }

        }
        $this->file = str_replace('%%swagger%%', $swagger, $this->file);
        $this->file = str_replace('%%properties%%', $properties, $this->file);
        
        if($hasTimestamp){
            $this->file = str_replace('%%has_timestamp%%', '//public $timestamps = false;', $this->file);
        }else {
            $this->file = str_replace('%%has_timestamp%%', 'public $timestamps = false;', $this->file);
        }

        if($hasDeleted){
            $this->file = str_replace('%%deleted%%', '/**
            * Configurar un filtro a todas las querys
            * @return void
            */
           protected static function boot(): void
           {
               parent::boot();
               
               static::addGlobalScope(\'exclude\', function (\Illuminate\Database\Eloquent\Builder $builder) {
                   $builder->where(\'%%name%%.deleted\', 0);
               });
           }', $this->file);
        }else {
            $this->file = str_replace('%%deleted%%', '', $this->file);
        }

        $this->file = str_replace('%%nameClass%%', $this->getCamelCase($this->name), $this->file);
        $this->file = str_replace('%%name%%', $this->name, $this->file);

        try {
            mkdir($this->savePath, 0777, true);
        } catch (\Exception $exc) { }
        file_put_contents($this->savePath . $this->getCamelCase($this->name) . '.php', $this->file);
    }
}
