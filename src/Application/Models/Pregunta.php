<?php

namespace App\Application\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Pregunta.
 * 
 * @author Adolfo Julcamoro <julcamorosm@gmail.com>
 * @version 1.0
 * @package App\Application\Models
 * @subpackage Controllers
 * @license https://opensource.org/licenses/MIT MIT License
 * @link https://github.com/adolfojulcamoro
 * @since 1.0
 */
class Pregunta extends Model
{
    protected $table = 'preguntas';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = ['id_banco_preguntas', 'pregunta', 'comentario', 'nota'];

    public function bancoPreguntas(): BelongsTo
    {
        return $this->belongsTo(BancoPreguntas::class, 'id_banco_preguntas', 'id');
    }

    public function respuestas(): HasMany
    {
        return $this->hasMany(Respuesta::class, 'id_pregunta', 'id');
    }
    
}