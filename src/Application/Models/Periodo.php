<?php

namespace App\Application\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Periodo.
 * 
 * @author Adolfo Julcamoro <julcamorosm@gmail.com>
 * @version 1.0
 * @package App\Application\Models
 * @subpackage Controllers
 * @license https://opensource.org/licenses/MIT MIT License
 * @link https://github.com/adolfojulcamoro
 * @since 1.0
 */
class Periodo extends Model
{
    protected $table = 'periodos';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = ['id_empresa_cliente', 'codigo', 'descripcion', 'activo', 'fecha_inicio', 'fecha_fin'];

    public function empresaCliente(): BelongsTo
    {
        return $this->belongsTo(EmpresaCliente::class, 'id_empresa_cliente', 'id');
    }

    public function clases(): HasMany
    {
        return $this->hasMany(Clase::class, 'id_periodo', 'id');
    }

    protected function activo(): Attribute 
    {
        return Attribute::make(
            get: fn($value) => intval($value),
            set: fn($value) => intval($value)
        );
    }
}
