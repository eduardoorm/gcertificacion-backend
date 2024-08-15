<?php

namespace App\Application\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class Trabajador.
 * 
 * @author Adolfo Julcamoro <julcamorosm@gmail.com>
 * @version 1.0
 * @package App\Application\Models
 * @subpackage Controllers
 * @license https://opensource.org/licenses/MIT MIT License
 * @link https://github.com/adolfojulcamoro
 * @since 1.0
 */
class Trabajador extends Model
{
    protected $table = 'trabajadores';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = ['id_empresa_cliente', 'nombres', 'apellidos', 'dni', 'area', 'puesto', 'sede', 'fecha_nacimiento','signature'];

    public function empresaCliente(): BelongsTo
    {
        return $this->belongsTo(EmpresaCliente::class, 'id_empresa_cliente', 'id');
    }

    public function usuario(): HasOne
    {
        return $this->hasOne(Usuario::class, 'id_trabajador', 'id');
    }

    public function clases(): BelongsToMany
    {
        return $this->belongsToMany(Clase::class, 'clases_trabajadores', 'id_trabajador', 'id_clase')
            ->withPivot('id', 'id_usuario', 'numero_intentos')->as('clases_trabajadores');
    }

    public function archivos(): BelongsToMany
    {
        return $this->belongsToMany(Archivo::class, 'archivos_trabajadores', 'id_trabajador', 'id_archivo')
            ->withPivot('id', 'descargado', 'aceptado')->as('archivos_trabajadores');
    }
}