<?php

namespace App\Application\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class ExamenAzarPregunta.
 * 
 * @author Adolfo Julcamoro <julcamorosm@gmail.com>
 * @version 1.0
 * @package App\Application\Models
 * @subpackage Controllers
 * @license https://opensource.org/licenses/MIT MIT License
 * @link https://github.com/adolfojulcamoro
 * @since 1.0
 */
class ExamenAzarPregunta extends Model
{
    protected $table = 'examen_azar_preguntas';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = ['id_examen_azar', 'id_pregunta'];

    public function respuestas(): HasMany
    {
        return $this->hasMany(Respuesta::class, 'id_pregunta', 'id_pregunta');
    }
}