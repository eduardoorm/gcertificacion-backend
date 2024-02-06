<?php

namespace App\Application\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class BancoPreguntas.
 * 
 * @author Adolfo Julcamoro <julcamorosm@gmail.com>
 * @version 1.0
 * @package App\Application\Models
 * @subpackage Controllers
 * @license https://opensource.org/licenses/MIT MIT License
 * @link https://github.com/adolfojulcamoro
 * @since 1.0
 */
class BancoPreguntas extends Model
{
    protected $table = 'banco_preguntas';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = ['id_clase', 'nombre', 'descripcion'];

    public function clase(): BelongsTo
    {
        return $this->belongsTo(Clase::class, 'id_clase', 'id');
    }

    public function preguntas(): HasMany
    {
        return $this->hasMany(Pregunta::class, 'id_banco_preguntas', 'id');
    }
}