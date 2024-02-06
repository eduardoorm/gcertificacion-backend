<?php

namespace App\Application\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class ClaseTrabajador.
 * 
 * @author Adolfo Julcamoro <julcamorosm@gmail.com>
 * @version 1.0
 * @package App\Application\Models
 * @subpackage Controllers
 * @license https://opensource.org/licenses/MIT MIT License
 * @link https://github.com/adolfojulcamoro
 * @since 1.0
 */
class ClaseTrabajador extends Model
{
    protected $table = 'clases_trabajadores';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = ['id_clase', 'id_trabajador', 'id_usuario', 'numero_intentos'];

    public function clase(): BelongsTo
    {
        return $this->belongsTo(Clase::class, 'id_clase', 'id');
    }

    public function trabajador(): BelongsTo
    {
        return $this->belongsTo(Trabajador::class, 'id_trabajador', 'id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id');
    }

    public function examenes(): HasMany
    {
        return $this->hasMany(ExamenAzar::class, 'id_clase_trabajador', 'id');
    }
}