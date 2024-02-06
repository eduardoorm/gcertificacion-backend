<?php

namespace App\Application\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class ExamenAzar.
 * 
 * @author Adolfo Julcamoro <julcamorosm@gmail.com>
 * @version 1.0
 * @package App\Application\Models
 * @subpackage Controllers
 * @license https://opensource.org/licenses/MIT MIT License
 * @link https://github.com/adolfojulcamoro
 * @since 1.0
 */
class ExamenAzar extends Model
{
    protected $table = 'examen_azar';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = [
        'id_clase_trabajador', 
        'numero_intento', 
        'fecha',
        'aprobado', 
        'respuestas_correctas', 
        'respuestas_incorrectas', 
        'nota', 
        'certificado', 
        'certificado_descargado'
    ];

    public function preguntas(): HasMany
    {
        return $this->hasMany(ExamenAzarPregunta::class, 'id_examen_azar', 'id');
    }

    public function clase_trabajador(): BelongsTo
    {
        return $this->belongsTo(ClaseTrabajador::class, 'id_clase_trabajador', 'id');
    }
}