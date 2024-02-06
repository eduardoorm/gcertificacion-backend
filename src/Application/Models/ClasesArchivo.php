<?php

namespace App\Application\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ClasesArchivo.
 * 
 * @author Adolfo Julcamoro <julcamorosm@gmail.com>
 * @version 1.0
 * @package App\Application\Models
 * @subpackage Controllers
 * @license https://opensource.org/licenses/MIT MIT License
 * @link https://github.com/adolfojulcamoro
 * @since 1.0
 */
class ClasesArchivo extends Model
{
    protected $table = 'clases_archivos';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = ['id_clase', 'id_archivo'];
}