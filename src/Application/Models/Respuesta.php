<?php

namespace App\Application\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Respuesta.
 * 
 * @author Adolfo Julcamoro <julcamorosm@gmail.com>
 * @version 1.0
 * @package App\Application\Models
 * @subpackage Controllers
 * @license https://opensource.org/licenses/MIT MIT License
 * @link https://github.com/adolfojulcamoro
 * @since 1.0
 */
class Respuesta extends Model
{
    protected $table = 'respuestas';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = ['id_pregunta', 'respuesta', 'correcta'];
    protected $guarded = ['correcta'];

    public function pregunta(): BelongsTo
    {
        return $this->belongsTo(Pregunta::class, 'id_pregunta', 'id');
    }

}