<?php

namespace App\Application\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class Clase.
 * 
 * @author Adolfo Julcamoro <julcamorosm@gmail.com>
 * @version 1.0
 * @package App\Application\Models
 * @subpackage Controllers
 * @license https://opensource.org/licenses/MIT MIT License
 * @link https://github.com/adolfojulcamoro
 * @since 1.0
 */
class Clase extends Model
{
    protected $table = 'clases';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = ['id_periodo', 'titulo', 'descripcion', 'tipo', 'fecha_inicio', 'fecha_fin', 'imagen'];

    public function periodo(): BelongsTo
    {
        return $this->belongsTo(Periodo::class, 'id_periodo', 'id');
    }
    
    /**
     * Defines a has-many relationship between the current model and the Archivo model.
     *
     * @return HasMany
     */
    public function archivos(): HasMany
    {
        return $this->hasMany(Archivo::class, 'id_clase', 'id');
    }

    public function bancoPreguntas(): HasOne
    {
        return $this->hasOne(BancoPreguntas::class, 'id_clase', 'id');
    }

    public function trabajadores(): BelongsToMany
    {
        return $this->belongsToMany(Trabajador::class, 'clases_trabajadores', 'id_clase', 'id_trabajador')
            ->withPivot('id', 'id_usuario', 'numero_intentos')->as('clases_trabajadores');
    }
}