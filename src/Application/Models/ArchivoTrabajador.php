<?php

namespace App\Application\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class ArchivoTrabajador.
 * 
 * @author Adolfo Julcamoro <julcamorosm@gmail.com>
 * @version 1.0
 * @package App\Application\Models
 * @subpackage Controllers
 * @license https://opensource.org/licenses/MIT MIT License
 * @link https://github.com/adolfojulcamoro
 * @since 1.0
 */
class ArchivoTrabajador extends Model
{
    protected $table = 'archivos_trabajadores';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = ['id_archivo', 'id_trabajador', 'descargado', 'aceptado'];

    public function archivo(): BelongsTo
    {
        return $this->belongsTo(Archivo::class, 'id_archivo', 'id');
    }

    public function trabajador(): BelongsTo
    {
        return $this->belongsTo(Trabajador::class, 'id_trabajador', 'id');
    }
}